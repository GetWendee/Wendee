<?php

namespace App\Http\Controllers;

use App\Services\PlacementCompatibilityService;

use App\Models\CabinetProfile;
use App\Models\Client;
use App\Models\User;
use App\Services\CabinetCompletionChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\AI\SuggestionAnalysisService;

class ClientController extends Controller
{
    private function nommerFichierPdf(string $libelle, Client $client): string
    {
        $nomClient = trim($client->prenom . ' ' . $client->nom);

        return $libelle . ' - ' . $nomClient . ' - ' . now()->format('d-m-Y') . '.pdf';
    }

    public function index(): View
    {
        $clients = Client::with('conseiller')->orderBy('nom')->paginate(20);

        return view('tenant.clients.index', ['clients' => $clients]);
    }

    public function create(): View|RedirectResponse
    {
        $cabinet = CabinetProfile::query()->first();

        if (! $cabinet || ! CabinetCompletionChecker::isComplete($cabinet, auth()->user())) {
            return redirect()->route('tenant.cabinet')
                ->with('cabinet_gate_redirect', true)
                ->with('status', "Complétez d'abord les informations essentielles de votre cabinet avant de créer un compte.");
        }

        return view('tenant.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $cabinet = CabinetProfile::query()->first();

        if (! $cabinet || ! CabinetCompletionChecker::isComplete($cabinet, $request->user())) {
            return redirect()->route('tenant.cabinet')
                ->with('cabinet_gate_redirect', true)
                ->with('status', "Complétez d'abord les informations essentielles de votre cabinet avant de créer un compte.");
        }

        $validated = $request->validate([
            'civilite' => ['nullable', 'string', 'in:M.,Mme'],
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'nom_jeune_fille' => ['nullable', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date'],
            'telephone_mobile' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]{10}$/'],
            'telephone_domicile' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]{10}$/'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:255'],
            'pays' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        if (! in_array($user->effectiveRole(), ['courtier', 'conseiller', 'apporteur'], true)) {
            abort(403);
        }

        $conseillerId = null;
        $apporteurId = null;

        if ($user->effectiveRole() === 'apporteur') {
            if (! $user->parent_id) {
                abort(403, 'Cet apporteur n’est rattaché à aucun conseiller.');
            }

            $conseillerId = $user->parent_id;
            $apporteurId = $user->id;
        } else {
            $conseillerId = $user->id;
        }

        $newUser = User::create([
            'name' => trim($validated['prenom'].' '.$validated['nom']),
            'email' => $validated['email'],
            'password' => Str::random(40),
            'role' => 'client',
            'parent_id' => $user->id,
            'activation_pending' => true,
        ]);

        $client = Client::create($validated + [
            'conseiller_id' => $conseillerId,
            'apporteur_id' => $apporteurId,
            'user_id' => $newUser->id,
        ]);

        Password::broker()->sendResetLink(['email' => $newUser->email]);

        return redirect()->route('tenant.clients.show', $client)->with('status', 'Client créé.');
    }

    public function show(Client $client, PlacementCompatibilityService $compatibility): View
    {
        $client->load([
            'kyc',
            'patrimoineElements',
            'profilInvestisseur',
            'conseiller',
            'apporteur',
        ]);

        $compatibilitesPlacements = $compatibility->evaluate(
            $client->profilInvestisseur
        );

        $patrimoine = $client->patrimoineElements;

        $actifsFinanciers = (float) $patrimoine
            ->where('categorie', 'actif_financier')
            ->sum('montant');

        $actifsNonFinanciers = (float) $patrimoine
            ->where('categorie', 'actif_non_financier')
            ->sum('montant');

        $actifs = $actifsFinanciers + $actifsNonFinanciers;

        $passifs = (float) $patrimoine
            ->where('categorie', 'passif')
            ->sum('montant');

        $revenus = (float) $patrimoine
            ->where('categorie', 'revenu')
            ->sum('montant');

        $charges = (float) $patrimoine
            ->where('categorie', 'charge')
            ->sum('montant');

        $patrimoineNet = $actifs - $passifs;
        $soldeAnnuel = $revenus - $charges;

        $repartitionPatrimoine = $patrimoine
            ->whereIn('categorie', ['actif_financier', 'actif_non_financier'])
            ->groupBy('nature')
            ->map(fn ($items) => (float) $items->sum('montant'))
            ->sortDesc();

        $maxRepartition = max(
            1,
            (float) $repartitionPatrimoine->max()
        );

        $rendezVousAVenir = $client->rendezVous()
            ->where('statut', 'confirme')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->get();

        return view('tenant.clients.show', compact(
            'client',
            'actifsFinanciers',
            'actifsNonFinanciers',
            'actifs',
            'passifs',
            'revenus',
            'charges',
            'patrimoineNet',
            'soldeAnnuel',
            'repartitionPatrimoine',
            'maxRepartition',
            'compatibilitesPlacements',
            'rendezVousAVenir',
        ));
    }

    public function edit(Client $client): View
    {
        return view('tenant.clients.edit', ['client' => $client]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'civilite' => ['nullable', 'string', 'in:M.,Mme'],
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'nom_jeune_fille' => ['nullable', 'string', 'max:255'],
            'date_naissance' => ['nullable', 'date'],
            'telephone_mobile' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]{10}$/'],
            'telephone_domicile' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]{10}$/'],
            'email' => ['required', 'email', 'max:255'],
            'adresse' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:10'],
            'ville' => ['nullable', 'string', 'max:255'],
            'pays' => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($validated);

        return redirect()->route('tenant.clients.show', $client)->with('status', 'Client mis à jour.');
    }

    public function aideDecision(Client $client): \Illuminate\View\View
    {
        $client->load([
            'kyc',
            'patrimoineElements',
            'profilInvestisseur',
            'conseiller',
            'apporteur',
        ]);

        /*
         * Dernière analyse IA terminée de chaque module.
         */
        $analysesDossier = $client->analyses()
            ->where('status', 'completed')
            ->whereIn('type', [
                'kyc',
                'patrimoine',
                'profil_investisseur',
            ])
            ->latest('created_at')
            ->get()
            ->groupBy('type')
            ->map(fn ($analyses) => $analyses->first());

        return view('tenant.clients.aide-decision', [
            'client' => $client,
            'analysesDossier' => $analysesDossier,
        ]);
    }


    public function recommandationPatrimoniale(
        Client $client
    ): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $suggestion = $client->analyses()
            ->where('type', 'suggestion')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();
        if (! $suggestion) {
            return redirect()
                ->route('tenant.clients.aide-decision', $client)
                ->with(
                    'error',
                    'La recommandation patrimoniale nécessite une suggestion de prestations générée au préalable.'
                );
        }
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $derniereRecommandation = $client->analyses()
            ->where('type', 'recommandation')
            ->latest('created_at')
            ->first();
        return view(
            'tenant.clients.recommandation-patrimoniale',
            [
                'client' => $client,
                'suggestion' => $suggestion,
                'cabinet' => $cabinet,
                'recommandation' => $derniereRecommandation,
            ]
        );
    }

    public function genererRecommandation(
        \Illuminate\Http\Request $request,
        Client $client,
        \App\Services\AI\RecommandationAnalysisService $recommandationAnalysis
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'contexte' => ['nullable', 'string', 'max:5000'],
            'missions' => ['nullable', 'array'],
            'missions.*' => [
                'string',
                'in:courtage_banque,courtage_assurance,conseil_investissement_financier',
            ],
            'montants' => ['nullable', 'array'],
            'montants.*' => ['nullable', 'numeric'],
            'taux' => ['nullable', 'array'],
            'taux.*' => ['nullable', 'numeric'],
        ]);

        $missionLabels = [
            'courtage_banque' => 'Mandat de courtage banque',
            'courtage_assurance' => 'Mandat de courtage assurance',
            'conseil_investissement_financier' => 'Conseils en investissements financiers (CIF)',
        ];
        $missionIndex = [
            'courtage_banque' => 0,
            'courtage_assurance' => 1,
            'conseil_investissement_financier' => 2,
        ];

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $prestations = $cabinet->prestations ?? [];

        $missionsRetenues = [];
        $total = 0.0;
        foreach ($validated['missions'] ?? [] as $key) {
            $index = $missionIndex[$key] ?? null;
            $mode = $index !== null ? ($prestations[$index]['mode'] ?? null) : null;
            $montant = (float) ($validated['montants'][$key] ?? 0);
            $taux = (float) ($validated['taux'][$key] ?? 0);
            $montantFinal = $mode === 'pourcentage'
                ? round($montant * $taux / 100, 2)
                : round($montant, 2);
            $missionsRetenues[] = [
                'key' => $key,
                'label' => $missionLabels[$key] ?? $key,
                'mode' => $mode,
                'montant' => $montantFinal,
            ];
            $total += $montantFinal;
        }

        try {
            $recommandationAnalysis->analyze($client, [
                'contexte' => $validated['contexte'] ?? '',
                'missions' => $missionsRetenues,
                'total' => round($total, 2),
            ]);
            return redirect()
                ->route('tenant.clients.recommandation-patrimoniale', $client)
                ->with('status', 'Recommandation patrimoniale générée.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                'Erreur génération recommandation patrimoniale',
                [
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]
            );
            return redirect()
                ->route('tenant.clients.recommandation-patrimoniale', $client)
                ->with('error', "La recommandation n'a pas pu être générée.");
        }
    }

    public function telechargerRecommandationPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $recommandation = $client->analyses()
            ->where('type', 'recommandation')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $recommandation) {
            return redirect()
                ->route('tenant.clients.recommandation-patrimoniale', $client)
                ->with('error', 'Aucune recommandation générée à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $corpsHtml = $recommandation->result_json['lettre_mission_html']
            ?? \App\Services\AI\RecommandationAnalysisService::convertirMarkdownEnHtml(
                $recommandation->result_json['lettre_mission'] ?? $recommandation->raw_response ?? ''
            );

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'recommandation' => $recommandation,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'corpsHtml' => $corpsHtml,
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.recommandation-patrimoniale',
            $data
        );

        $filename = $this->nommerFichierPdf('Recommandation patrimoniale', $client);

        return $pdf->download($filename);
    }




    public function modifierRecommandationContenu(
        \Illuminate\Http\Request $request,
        Client $client,
        \App\Models\ClientAnalysis $analysis
    ): \Illuminate\Http\RedirectResponse
    {
        abort_unless($analysis->client_id === $client->id, 404);
        abort_unless($analysis->type === 'recommandation', 404);
        $validated = $request->validate([
            'contenu_html' => ['required', 'string'],
        ]);
        $contenuNettoye = strip_tags(
            $validated['contenu_html'],
            '<h2><span><p><strong><br><ul><ol><li><em>'
        );
        $resultJson = $analysis->result_json ?? [];
        $resultJson['lettre_mission_html'] = $contenuNettoye;
        $analysis->update(['result_json' => $resultJson]);
        return redirect()
            ->route('tenant.clients.recommandation-patrimoniale', $client)
            ->with('status', 'Modifications enregistrées.');
    }

    public function mission(
        Client $client
    ): \Illuminate\View\View
    {
        return view('tenant.clients.mission', [
            'client' => $client,
        ]);
    }

    public function contratsClients(
        Client $client
    ): \Illuminate\View\View
    {
        return view('tenant.clients.contrats-clients', [
            'client' => $client,
        ]);
    }

    public function telechargerKycPdf(Client $client): \Symfony\Component\HttpFoundation\Response
    {
        $resultat = app(\App\Services\ClientPdfService::class)->kyc($client);

        return $resultat['pdf']->download($resultat['filename']);
    }

    private function typesBibliotheque(): array
    {
        return [
            'recommandation' => ['label' => 'Recommandation patrimoniale', 'categorie' => 'recommandations', 'categorie_label' => 'Recommandation', 'route' => 'tenant.clients.recommandation-patrimoniale.pdf'],
            'plan_action' => ['label' => "Plan d'action", 'categorie' => 'plans-actions', 'categorie_label' => "Plan d'action", 'route' => 'tenant.clients.plan-action.pdf'],
            'lettre_mission_scpi' => ['label' => 'Lettre de mission SCPI', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.lettre-mission-scpi.pdf'],
            'mandat_assurance_vie' => ['label' => 'Mandat assurance vie', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-assurance-vie.pdf'],
            'mandat_assurance_deces' => ['label' => 'Mandat assurance décès', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-assurance-deces.pdf'],
            'mandat_assurance_emprunteur' => ['label' => 'Mandat assurance emprunteur', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-assurance-emprunteur.pdf'],
            'mandat_assurance_habitation' => ['label' => 'Mandat assurance habitation', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-assurance-habitation.pdf'],
            'mandat_assurance_obseques' => ['label' => 'Mandat assurance obsèques', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-assurance-obseques.pdf'],
            'mandat_complementaire_sante' => ['label' => 'Mandat complémentaire santé', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-complementaire-sante.pdf'],
            'mandat_contrat_capitalisation' => ['label' => 'Mandat contrat de capitalisation', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-contrat-capitalisation.pdf'],
            'mandat_garantie_accident_vie' => ['label' => 'Mandat garantie accident de la vie', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-garantie-accident-vie.pdf'],
            'mandat_assurance_vehicule' => ['label' => 'Mandat assurance véhicule', 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-assurance-vehicule.pdf'],
            'mandat_plan_epargne_retraite' => ["label" => "Mandat plan d'épargne retraite", 'categorie' => 'mandats', 'categorie_label' => 'Mandat', 'route' => 'tenant.clients.mandat-plan-epargne-retraite.pdf'],
            'kyc' => ['label' => 'Recueil de connaissance client', 'categorie' => 'informations-client', 'categorie_label' => 'Informations client', 'route' => 'tenant.clients.kyc.pdf'],
        ];
    }

    private function documentsBibliotheque(Client $client): \Illuminate\Support\Collection
    {
        return collect($this->typesBibliotheque())->map(function ($meta, $type) use ($client) {
            $analyse = $client->analyses()
                ->where('type', $type)
                ->where('status', 'completed')
                ->latest('completed_at')
                ->first();

            if (! $analyse) {
                return null;
            }

            return [
                'label' => $meta['label'],
                'categorie' => $meta['categorie'],
                'categorie_label' => $meta['categorie_label'],
                'date' => $analyse->completed_at,
                'url' => route($meta['route'], $client),
            ];
        })->filter()->sortByDesc('date')->values();
    }

    public function conformitesClients(
        Client $client
    ): \Illuminate\View\View
    {
        $documents = $this->documentsBibliotheque($client);

        $fichiersPersonnels = $client->documents()->get()->keyBy('type');

        return view('tenant.clients.conformites-clients', [
            'client' => $client,
            'documents' => $documents->take(10)->values(),
            'documentsTotal' => $documents->count(),
            'fichiersPersonnels' => $fichiersPersonnels,
            'typesDocumentsPersonnels' => \App\Http\Controllers\ClientDocumentController::TYPES,
        ]);
    }

    public function rechercherDocumentsBibliotheque(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\JsonResponse
    {
        $documents = $this->documentsBibliotheque($client);

        $categorie = $request->query('categorie', 'tous');
        if ($categorie !== 'tous') {
            $documents = $documents->where('categorie', $categorie)->values();
        }

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $documents = $documents->filter(
                fn ($document) => str_contains(mb_strtolower($document['label']), mb_strtolower($q))
            )->values();
        }

        $total = $documents->count();
        $parPage = 10;
        $dernierePage = max(1, (int) ceil($total / $parPage));
        $page = max(1, min((int) $request->query('page', 1), $dernierePage));

        $rows = $documents->forPage($page, $parPage)->values()->map(fn ($document) => [
            'label' => $document['label'],
            'categorie' => $document['categorie'],
            'categorie_label' => $document['categorie_label'],
            'date' => $document['date']->translatedFormat('d M. Y'),
            'url' => $document['url'],
        ]);

        return response()->json([
            'rows' => $rows,
            'page' => $page,
            'derniere_page' => $dernierePage,
            'total' => $total,
        ]);
    }

    public function mandatAssuranceVie(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_vie')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-assurance-vie', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatAssuranceVie(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'objectifs' => ['nullable', 'array'],
            'objectifs.*' => ['string', 'in:epargne,retraite,transmission,fiscalite,disponibilite,autre'],
            'objectif_autre_precision' => ['nullable', 'string', 'max:255'],
            'horizon' => ['nullable', 'string', 'in:moins_2_ans,2_5_ans,5_8_ans,plus_8_ans'],
            'versement_initial' => ['nullable', 'numeric'],
            'versement_programme_montant' => ['nullable', 'numeric'],
            'versement_programme_periodicite' => ['nullable', 'string', 'in:mensuel,trimestriel,annuel'],
            'contrats_existants' => ['nullable', 'array'],
            'contrats_existants.*.assureur' => ['nullable', 'string', 'max:255'],
            'contrats_existants.*.valeur_actuelle' => ['nullable', 'numeric'],
            'contrats_existants.*.date_ouverture' => ['nullable', 'date'],
            'beneficiaires' => ['nullable', 'array'],
            'beneficiaires.*.nom' => ['nullable', 'string', 'max:255'],
            'beneficiaires.*.lien' => ['nullable', 'string', 'max:255'],
            'beneficiaires.*.quote_part' => ['nullable', 'string', 'max:100'],
            'clause_beneficiaire_type' => ['nullable', 'string', 'in:standard,personnalisee'],
            'clause_beneficiaire_texte' => ['nullable', 'string', 'max:2000'],
            'mode_gestion' => ['nullable', 'string', 'in:libre,pilotee,mandat'],
            'repartition_risque' => ['nullable', 'string', 'in:prudent,equilibre,dynamique'],
            'rachat_possible' => ['nullable', 'string', 'in:oui,non'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['contrats_existants'] = array_values(array_filter(
            $validated['contrats_existants'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));
        $validated['beneficiaires'] = array_values(array_filter(
            $validated['beneficiaires'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_assurance_vie',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-assurance-vie', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatAssuranceViePdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_vie')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-assurance-vie', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $objectifsLabels = [
            'epargne' => 'Constituer une épargne',
            'retraite' => 'Préparer la retraite',
            'transmission' => 'Transmettre un capital',
            'fiscalite' => 'Optimiser la fiscalité',
            'disponibilite' => 'Disponibilité immédiate',
            'autre' => 'Autre',
        ];
        $horizonLabels = [
            'moins_2_ans' => 'Moins de 2 ans',
            '2_5_ans' => '2 à 5 ans',
            '5_8_ans' => '5 à 8 ans',
            'plus_8_ans' => 'Plus de 8 ans',
        ];
        $periodiciteLabels = ['mensuel' => 'Mensuel', 'trimestriel' => 'Trimestriel', 'annuel' => 'Annuel'];
        $modeGestionLabels = ['libre' => 'Gestion libre', 'pilotee' => 'Gestion pilotée', 'mandat' => 'Gestion sous mandat'];
        $repartitionLabels = ['prudent' => 'Prudent', 'equilibre' => 'Équilibré', 'dynamique' => 'Dynamique'];

        $clauseTexte = ($donnees['clause_beneficiaire_type'] ?? 'standard') === 'personnalisee'
            ? ($donnees['clause_beneficiaire_texte'] ?? '')
            : 'Mon conjoint, à défaut mes enfants nés ou à naître, vivants ou représentés, à défaut mes héritiers.';

        $recueil = [
            'objectifs_libelle' => implode(', ', array_map(
                fn ($v) => $objectifsLabels[$v] ?? $v,
                $donnees['objectifs'] ?? []
            )) ?: '-',
            'objectif_autre_precision' => $donnees['objectif_autre_precision'] ?? null,
            'horizon_libelle' => $horizonLabels[$donnees['horizon'] ?? ''] ?? '-',
            'versement_initial' => $donnees['versement_initial'] ?? null,
            'versement_programme_montant' => $donnees['versement_programme_montant'] ?? null,
            'versement_programme_periodicite_libelle' => $periodiciteLabels[$donnees['versement_programme_periodicite'] ?? ''] ?? '-',
            'contrats_existants' => $donnees['contrats_existants'] ?? [],
            'beneficiaires' => $donnees['beneficiaires'] ?? [],
            'clause_beneficiaire_texte_finale' => $clauseTexte,
            'mode_gestion_libelle' => $modeGestionLabels[$donnees['mode_gestion'] ?? ''] ?? '-',
            'repartition_risque_libelle' => $repartitionLabels[$donnees['repartition_risque'] ?? ''] ?? '-',
            'rachat_possible_libelle' => ($donnees['rachat_possible'] ?? '') === 'oui' ? 'Oui' : (($donnees['rachat_possible'] ?? '') === 'non' ? 'Non' : '-'),
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-assurance-vie',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat assurance vie', $client);

        return $pdf->download($filename);
    }

    public function mandatAssuranceDeces(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_deces')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-assurance-deces', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatAssuranceDeces(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'objectif_garantie' => ['nullable', 'string', 'in:conjoint,enfants,pret,transmission,professionnel,autre'],
            'objectif_autre_precision' => ['nullable', 'string', 'max:255'],
            'capital_assure_souhaite' => ['nullable', 'numeric'],
            'duree_contrat' => ['nullable', 'string', 'in:temporaire_duree,jusqu_age,viager,adosse_pret'],
            'duree_temporaire_annees' => ['nullable', 'integer'],
            'age_terme' => ['nullable', 'integer'],
            'pret_capital_restant_du' => ['nullable', 'numeric'],
            'pret_duree_restante' => ['nullable', 'integer'],
            'garanties_complementaires' => ['nullable', 'array'],
            'garanties_complementaires.*' => ['string', 'in:ptia,invalidite,rente_education,rente_conjoint,double_effet,exoneration_cotisations'],
            'rente_education_montant' => ['nullable', 'numeric'],
            'rente_education_age_limite' => ['nullable', 'integer'],
            'beneficiaires' => ['nullable', 'array'],
            'beneficiaires.*.nom' => ['nullable', 'string', 'max:255'],
            'beneficiaires.*.lien' => ['nullable', 'string', 'max:255'],
            'beneficiaires.*.quote_part' => ['nullable', 'string', 'max:100'],
            'clause_beneficiaire_type' => ['nullable', 'string', 'in:standard,personnalisee'],
            'clause_beneficiaire_texte' => ['nullable', 'string', 'max:2000'],
            'demembrement_clause' => ['nullable', 'string', 'in:oui,non'],
            'fumeur' => ['nullable', 'string', 'in:oui,non'],
            'taille_cm' => ['nullable', 'integer'],
            'poids_kg' => ['nullable', 'integer'],
            'profession_a_risque' => ['nullable', 'string', 'in:oui,non'],
            'sports_a_risque' => ['nullable', 'array'],
            'sports_a_risque.*' => ['string', 'in:aeriens,mecaniques,plongee,montagne,autre'],
            'sports_a_risque_autre_precision' => ['nullable', 'string', 'max:255'],
            'antecedents_medicaux' => ['nullable', 'string', 'in:oui,non'],
            'traitement_en_cours' => ['nullable', 'string', 'in:oui,non'],
            'antecedents_precision' => ['nullable', 'string', 'max:2000'],
            'hospitalisation_5_ans' => ['nullable', 'string', 'in:oui,non'],
            'arret_travail_3_ans' => ['nullable', 'string', 'in:oui,non'],
            'revenu_annuel_foyer' => ['nullable', 'numeric'],
            'charges_mensuelles_fixes' => ['nullable', 'numeric'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['beneficiaires'] = array_values(array_filter(
            $validated['beneficiaires'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_assurance_deces',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-assurance-deces', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatAssuranceDecesPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_deces')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-assurance-deces', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $objectifLabels = [
            'conjoint' => 'Protéger le conjoint',
            'enfants' => 'Protéger les enfants',
            'pret' => 'Couvrir un prêt',
            'transmission' => 'Transmettre un capital',
            'professionnel' => 'Obligations professionnelles',
            'autre' => 'Autre',
        ];
        $dureeLabels = [
            'temporaire_duree' => 'Temporaire, durée fixe',
            'jusqu_age' => "Jusqu'à un âge donné",
            'viager' => 'Viager',
            'adosse_pret' => 'Adossé à un prêt',
        ];
        $garantiesLabels = [
            'ptia' => "Perte totale et irréversible d'autonomie (PTIA)",
            'invalidite' => 'Invalidité complémentaire',
            'rente_education' => 'Rente éducation',
            'rente_conjoint' => 'Rente de conjoint',
            'double_effet' => 'Double effet',
            'exoneration_cotisations' => 'Exonération des cotisations en cas d\'invalidité',
        ];
        $sportsLabels = [
            'aeriens' => 'Sports aériens',
            'mecaniques' => 'Sports mécaniques',
            'plongee' => 'Plongée sous-marine',
            'montagne' => 'Alpinisme / haute montagne',
            'autre' => 'Autre',
        ];
        $ouiNon = fn ($valeur) => $valeur === 'oui' ? 'Oui' : ($valeur === 'non' ? 'Non' : '-');

        $clauseTexte = ($donnees['clause_beneficiaire_type'] ?? 'standard') === 'personnalisee'
            ? ($donnees['clause_beneficiaire_texte'] ?? '')
            : 'Mon conjoint, à défaut mes enfants nés ou à naître, vivants ou représentés, à défaut mes héritiers.';

        $recueil = [
            'objectif_garantie_libelle' => $objectifLabels[$donnees['objectif_garantie'] ?? ''] ?? '-',
            'objectif_autre_precision' => $donnees['objectif_autre_precision'] ?? null,
            'capital_assure_souhaite' => $donnees['capital_assure_souhaite'] ?? null,
            'duree_contrat_libelle' => $dureeLabels[$donnees['duree_contrat'] ?? ''] ?? '-',
            'duree_temporaire_annees' => $donnees['duree_temporaire_annees'] ?? null,
            'age_terme' => $donnees['age_terme'] ?? null,
            'pret_capital_restant_du' => $donnees['pret_capital_restant_du'] ?? null,
            'pret_duree_restante' => $donnees['pret_duree_restante'] ?? null,
            'garanties_complementaires_libelle' => implode(', ', array_map(
                fn ($v) => $garantiesLabels[$v] ?? $v,
                $donnees['garanties_complementaires'] ?? []
            )) ?: '-',
            'rente_education_montant' => $donnees['rente_education_montant'] ?? null,
            'rente_education_age_limite' => $donnees['rente_education_age_limite'] ?? null,
            'beneficiaires' => $donnees['beneficiaires'] ?? [],
            'clause_beneficiaire_texte_finale' => $clauseTexte,
            'demembrement_clause_libelle' => $ouiNon($donnees['demembrement_clause'] ?? ''),
            'fumeur_libelle' => $ouiNon($donnees['fumeur'] ?? ''),
            'taille_cm' => $donnees['taille_cm'] ?? null,
            'poids_kg' => $donnees['poids_kg'] ?? null,
            'profession_a_risque_libelle' => $ouiNon($donnees['profession_a_risque'] ?? ''),
            'sports_a_risque_libelle' => implode(', ', array_map(
                fn ($v) => $sportsLabels[$v] ?? $v,
                $donnees['sports_a_risque'] ?? []
            )) ?: 'Aucun',
            'antecedents_medicaux_libelle' => $ouiNon($donnees['antecedents_medicaux'] ?? ''),
            'traitement_en_cours_libelle' => $ouiNon($donnees['traitement_en_cours'] ?? ''),
            'antecedents_precision' => $donnees['antecedents_precision'] ?? null,
            'hospitalisation_5_ans_libelle' => $ouiNon($donnees['hospitalisation_5_ans'] ?? ''),
            'arret_travail_3_ans_libelle' => $ouiNon($donnees['arret_travail_3_ans'] ?? ''),
            'revenu_annuel_foyer' => $donnees['revenu_annuel_foyer'] ?? null,
            'charges_mensuelles_fixes' => $donnees['charges_mensuelles_fixes'] ?? null,
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-assurance-deces',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat assurance décès', $client);

        return $pdf->download($filename);
    }

    public function mandatAssuranceEmprunteur(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_emprunteur')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-assurance-emprunteur', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatAssuranceEmprunteur(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'type_pret' => ['nullable', 'string', 'in:immobilier,consommation,professionnel,autre'],
            'type_pret_autre_precision' => ['nullable', 'string', 'max:255'],
            'montant_emprunte' => ['nullable', 'numeric'],
            'duree_pret' => ['nullable', 'integer'],
            'taux_pret' => ['nullable', 'numeric'],
            'date_offre_pret' => ['nullable', 'date'],
            'emprunteurs' => ['nullable', 'array'],
            'emprunteurs.*.nom' => ['nullable', 'string', 'max:255'],
            'emprunteurs.*.quotite_pourcentage' => ['nullable', 'numeric'],
            'garanties_souhaitees' => ['nullable', 'array'],
            'garanties_souhaitees.*' => ['string', 'in:deces,ptia,ipt,itt,invalidite,perte_emploi'],
            'type_couverture' => ['nullable', 'string', 'in:capital_initial_constant,capital_restant_du'],
            'fumeur' => ['nullable', 'string', 'in:oui,non'],
            'taille_cm' => ['nullable', 'integer'],
            'poids_kg' => ['nullable', 'integer'],
            'profession_a_risque' => ['nullable', 'string', 'in:oui,non'],
            'sports_a_risque' => ['nullable', 'array'],
            'sports_a_risque.*' => ['string', 'in:aeriens,mecaniques,plongee,montagne,autre'],
            'sports_a_risque_autre_precision' => ['nullable', 'string', 'max:255'],
            'antecedents_medicaux' => ['nullable', 'string', 'in:oui,non'],
            'traitement_en_cours' => ['nullable', 'string', 'in:oui,non'],
            'antecedents_precision' => ['nullable', 'string', 'max:2000'],
            'arret_travail_recent' => ['nullable', 'string', 'in:oui,non'],
            'statut_professionnel' => ['nullable', 'string', 'in:salarie,independant,fonctionnaire,autre'],
            'statut_professionnel_autre_precision' => ['nullable', 'string', 'max:255'],
            'anciennete_annees' => ['nullable', 'integer'],
            'revenu_annuel' => ['nullable', 'numeric'],
            'delegation_assurance' => ['nullable', 'string', 'in:oui,non'],
            'assureur_actuel' => ['nullable', 'string', 'max:255'],
            'date_echeance_actuelle' => ['nullable', 'date'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['emprunteurs'] = array_values(array_filter(
            $validated['emprunteurs'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_assurance_emprunteur',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-assurance-emprunteur', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatAssuranceEmprunteurPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_emprunteur')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-assurance-emprunteur', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $typePretLabels = [
            'immobilier' => 'Prêt immobilier',
            'consommation' => 'Prêt à la consommation',
            'professionnel' => 'Prêt professionnel',
            'autre' => 'Autre',
        ];
        $garantiesLabels = [
            'deces' => 'Décès',
            'ptia' => "Perte totale et irréversible d'autonomie (PTIA)",
            'ipt' => 'Invalidité permanente totale (IPT)',
            'itt' => 'Incapacité temporaire totale de travail (ITT)',
            'invalidite' => 'Invalidité permanente partielle',
            'perte_emploi' => "Perte d'emploi",
        ];
        $typeCouvertureLabels = [
            'capital_initial_constant' => 'Capital initial constant',
            'capital_restant_du' => 'Capital restant dû',
        ];
        $sportsLabels = [
            'aeriens' => 'Sports aériens',
            'mecaniques' => 'Sports mécaniques',
            'plongee' => 'Plongée sous-marine',
            'montagne' => 'Alpinisme / haute montagne',
            'autre' => 'Autre',
        ];
        $statutLabels = [
            'salarie' => 'Salarié',
            'independant' => 'Indépendant',
            'fonctionnaire' => 'Fonctionnaire',
            'autre' => 'Autre',
        ];
        $ouiNon = fn ($valeur) => $valeur === 'oui' ? 'Oui' : ($valeur === 'non' ? 'Non' : '-');

        $recueil = [
            'type_pret_libelle' => $typePretLabels[$donnees['type_pret'] ?? ''] ?? '-',
            'type_pret_autre_precision' => $donnees['type_pret_autre_precision'] ?? null,
            'montant_emprunte' => $donnees['montant_emprunte'] ?? null,
            'duree_pret' => $donnees['duree_pret'] ?? null,
            'taux_pret' => $donnees['taux_pret'] ?? null,
            'date_offre_pret' => $donnees['date_offre_pret'] ?? null,
            'emprunteurs' => $donnees['emprunteurs'] ?? [],
            'garanties_souhaitees_libelle' => implode(', ', array_map(
                fn ($v) => $garantiesLabels[$v] ?? $v,
                $donnees['garanties_souhaitees'] ?? []
            )) ?: '-',
            'type_couverture_libelle' => $typeCouvertureLabels[$donnees['type_couverture'] ?? ''] ?? '-',
            'fumeur_libelle' => $ouiNon($donnees['fumeur'] ?? ''),
            'taille_cm' => $donnees['taille_cm'] ?? null,
            'poids_kg' => $donnees['poids_kg'] ?? null,
            'profession_a_risque_libelle' => $ouiNon($donnees['profession_a_risque'] ?? ''),
            'sports_a_risque_libelle' => implode(', ', array_map(
                fn ($v) => $sportsLabels[$v] ?? $v,
                $donnees['sports_a_risque'] ?? []
            )) ?: 'Aucun',
            'antecedents_medicaux_libelle' => $ouiNon($donnees['antecedents_medicaux'] ?? ''),
            'traitement_en_cours_libelle' => $ouiNon($donnees['traitement_en_cours'] ?? ''),
            'antecedents_precision' => $donnees['antecedents_precision'] ?? null,
            'arret_travail_recent_libelle' => $ouiNon($donnees['arret_travail_recent'] ?? ''),
            'statut_professionnel_libelle' => $statutLabels[$donnees['statut_professionnel'] ?? ''] ?? '-',
            'statut_professionnel_autre_precision' => $donnees['statut_professionnel_autre_precision'] ?? null,
            'anciennete_annees' => $donnees['anciennete_annees'] ?? null,
            'revenu_annuel' => $donnees['revenu_annuel'] ?? null,
            'delegation_assurance_libelle' => $ouiNon($donnees['delegation_assurance'] ?? ''),
            'assureur_actuel' => $donnees['assureur_actuel'] ?? null,
            'date_echeance_actuelle' => $donnees['date_echeance_actuelle'] ?? null,
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-assurance-emprunteur',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat assurance emprunteur', $client);

        return $pdf->download($filename);
    }

    public function mandatAssuranceHabitation(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_habitation')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-assurance-habitation', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatAssuranceHabitation(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'type_bien' => ['nullable', 'string', 'in:residence_principale,residence_secondaire,locatif'],
            'statut_occupation' => ['nullable', 'string', 'in:proprietaire,locataire'],
            'type_logement' => ['nullable', 'string', 'in:maison,appartement'],
            'surface_m2' => ['nullable', 'integer'],
            'nombre_pieces' => ['nullable', 'integer'],
            'annee_construction' => ['nullable', 'integer'],
            'adresse_bien' => ['nullable', 'string', 'max:255'],
            'code_postal_bien' => ['nullable', 'string', 'max:10'],
            'ville_bien' => ['nullable', 'string', 'max:255'],
            'nombre_personnes_foyer' => ['nullable', 'integer'],
            'presence_animaux' => ['nullable', 'string', 'in:oui,non'],
            'garanties_souhaitees' => ['nullable', 'array'],
            'garanties_souhaitees.*' => ['string', 'in:degats_eaux,incendie_explosion,vol_vandalisme,bris_de_glace,catastrophes_naturelles,responsabilite_civile,protection_juridique,jardin_dependances'],
            'biens_valeur' => ['nullable', 'array'],
            'biens_valeur.*.nature' => ['nullable', 'string', 'max:255'],
            'biens_valeur.*.valeur_estimee' => ['nullable', 'numeric'],
            'securite_logement' => ['nullable', 'array'],
            'securite_logement.*' => ['string', 'in:alarme,telesurveillance,porte_blindee'],
            'sinistres_anterieurs' => ['nullable', 'string', 'in:oui,non'],
            'sinistres' => ['nullable', 'array'],
            'sinistres.*.nature' => ['nullable', 'string', 'max:255'],
            'sinistres.*.date' => ['nullable', 'date'],
            'sinistres.*.montant_indemnise' => ['nullable', 'numeric'],
            'assureur_actuel' => ['nullable', 'string', 'max:255'],
            'date_echeance_actuelle' => ['nullable', 'date'],
            'resiliation_hamon' => ['nullable', 'string', 'in:oui,non'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['biens_valeur'] = array_values(array_filter(
            $validated['biens_valeur'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));
        $validated['sinistres'] = array_values(array_filter(
            $validated['sinistres'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_assurance_habitation',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-assurance-habitation', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatAssuranceHabitationPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_habitation')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-assurance-habitation', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $typeBienLabels = [
            'residence_principale' => 'Résidence principale',
            'residence_secondaire' => 'Résidence secondaire',
            'locatif' => 'Bien mis en location',
        ];
        $statutOccupationLabels = [
            'proprietaire' => 'Propriétaire',
            'locataire' => 'Locataire',
        ];
        $typeLogementLabels = [
            'maison' => 'Maison',
            'appartement' => 'Appartement',
        ];
        $garantiesLabels = [
            'degats_eaux' => 'Dégâts des eaux',
            'incendie_explosion' => 'Incendie / explosion',
            'vol_vandalisme' => 'Vol / vandalisme',
            'bris_de_glace' => 'Bris de glace',
            'catastrophes_naturelles' => 'Catastrophes naturelles',
            'responsabilite_civile' => 'Responsabilité civile',
            'protection_juridique' => 'Protection juridique',
            'jardin_dependances' => 'Jardin / dépendances',
        ];
        $securiteLabels = [
            'alarme' => 'Alarme',
            'telesurveillance' => 'Télésurveillance',
            'porte_blindee' => 'Porte blindée',
        ];
        $ouiNon = fn ($valeur) => $valeur === 'oui' ? 'Oui' : ($valeur === 'non' ? 'Non' : '-');

        $adresseBien = trim(($donnees['adresse_bien'] ?? '') . ', ' . ($donnees['code_postal_bien'] ?? '') . ' ' . ($donnees['ville_bien'] ?? ''), ' ,');

        $recueil = [
            'type_bien_libelle' => $typeBienLabels[$donnees['type_bien'] ?? ''] ?? '-',
            'statut_occupation_libelle' => $statutOccupationLabels[$donnees['statut_occupation'] ?? ''] ?? '-',
            'type_logement_libelle' => $typeLogementLabels[$donnees['type_logement'] ?? ''] ?? '-',
            'surface_m2' => $donnees['surface_m2'] ?? null,
            'nombre_pieces' => $donnees['nombre_pieces'] ?? null,
            'annee_construction' => $donnees['annee_construction'] ?? null,
            'adresse_bien_complete' => $adresseBien ?: 'Identique à l\'adresse du client',
            'nombre_personnes_foyer' => $donnees['nombre_personnes_foyer'] ?? null,
            'presence_animaux_libelle' => $ouiNon($donnees['presence_animaux'] ?? ''),
            'garanties_souhaitees_libelle' => implode(', ', array_map(
                fn ($v) => $garantiesLabels[$v] ?? $v,
                $donnees['garanties_souhaitees'] ?? []
            )) ?: '-',
            'biens_valeur' => $donnees['biens_valeur'] ?? [],
            'securite_logement_libelle' => implode(', ', array_map(
                fn ($v) => $securiteLabels[$v] ?? $v,
                $donnees['securite_logement'] ?? []
            )) ?: 'Aucun',
            'sinistres_anterieurs_libelle' => $ouiNon($donnees['sinistres_anterieurs'] ?? ''),
            'sinistres' => $donnees['sinistres'] ?? [],
            'assureur_actuel' => $donnees['assureur_actuel'] ?? null,
            'date_echeance_actuelle' => $donnees['date_echeance_actuelle'] ?? null,
            'resiliation_hamon_libelle' => $ouiNon($donnees['resiliation_hamon'] ?? ''),
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-assurance-habitation',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat assurance habitation', $client);

        return $pdf->download($filename);
    }

    public function mandatAssuranceObseques(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_obseques')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-assurance-obseques', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatAssuranceObseques(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'objectif_contrat' => ['nullable', 'string', 'in:financer_obseques,financer_obseques_plus_transmission'],
            'type_contrat' => ['nullable', 'string', 'in:capital_deces,prestations'],
            'capital_souhaite' => ['nullable', 'numeric'],
            'modalite_versement' => ['nullable', 'string', 'in:unique,periodique'],
            'montant_versement_periodique' => ['nullable', 'numeric'],
            'periodicite_versement' => ['nullable', 'string', 'in:mensuel,trimestriel,annuel'],
            'type_ceremonie' => ['nullable', 'string', 'in:inhumation,cremation,indifferent'],
            'lieu_souhaite' => ['nullable', 'string', 'max:255'],
            'prestataire_pressenti' => ['nullable', 'string', 'max:255'],
            'precisions_obseques' => ['nullable', 'string', 'max:2000'],
            'organisateurs' => ['nullable', 'array'],
            'organisateurs.*.nom' => ['nullable', 'string', 'max:255'],
            'organisateurs.*.lien' => ['nullable', 'string', 'max:255'],
            'organisateurs.*.telephone' => ['nullable', 'string', 'max:30'],
            'age_assure' => ['nullable', 'integer'],
            'affection_grave_declaree' => ['nullable', 'string', 'in:oui,non'],
            'affection_grave_precision' => ['nullable', 'string', 'max:2000'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['organisateurs'] = array_values(array_filter(
            $validated['organisateurs'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_assurance_obseques',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-assurance-obseques', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatAssuranceObsequesPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_obseques')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-assurance-obseques', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $objectifLabels = [
            'financer_obseques' => 'Financer uniquement les obsèques',
            'financer_obseques_plus_transmission' => 'Financer les obsèques et transmettre un reliquat',
        ];
        $typeContratLabels = [
            'capital_deces' => 'Capital décès simple',
            'prestations' => 'Contrat en prestations (organisation prise en charge)',
        ];
        $modaliteLabels = [
            'unique' => 'Versement unique',
            'periodique' => 'Versements périodiques',
        ];
        $periodiciteLabels = ['mensuel' => 'Mensuel', 'trimestriel' => 'Trimestriel', 'annuel' => 'Annuel'];
        $typeCeremonieLabels = [
            'inhumation' => 'Inhumation',
            'cremation' => 'Crémation',
            'indifferent' => 'Indifférent',
        ];
        $ouiNon = fn ($valeur) => $valeur === 'oui' ? 'Oui' : ($valeur === 'non' ? 'Non' : '-');

        $recueil = [
            'objectif_contrat_libelle' => $objectifLabels[$donnees['objectif_contrat'] ?? ''] ?? '-',
            'type_contrat_libelle' => $typeContratLabels[$donnees['type_contrat'] ?? ''] ?? '-',
            'capital_souhaite' => $donnees['capital_souhaite'] ?? null,
            'modalite_versement_libelle' => $modaliteLabels[$donnees['modalite_versement'] ?? ''] ?? '-',
            'montant_versement_periodique' => $donnees['montant_versement_periodique'] ?? null,
            'periodicite_versement_libelle' => $periodiciteLabels[$donnees['periodicite_versement'] ?? ''] ?? '-',
            'type_ceremonie_libelle' => $typeCeremonieLabels[$donnees['type_ceremonie'] ?? ''] ?? '-',
            'lieu_souhaite' => $donnees['lieu_souhaite'] ?? null,
            'prestataire_pressenti' => $donnees['prestataire_pressenti'] ?? null,
            'precisions_obseques' => $donnees['precisions_obseques'] ?? null,
            'organisateurs' => $donnees['organisateurs'] ?? [],
            'age_assure' => $donnees['age_assure'] ?? null,
            'affection_grave_declaree_libelle' => $ouiNon($donnees['affection_grave_declaree'] ?? ''),
            'affection_grave_precision' => $donnees['affection_grave_precision'] ?? null,
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-assurance-obseques',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat assurance obsèques', $client);

        return $pdf->download($filename);
    }

    public function mandatComplementaireSante(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_complementaire_sante')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-complementaire-sante', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatComplementaireSante(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'regime_obligatoire' => ['nullable', 'string', 'in:salarie,tns,fonctionnaire,retraite,sans_emploi,autre'],
            'regime_obligatoire_autre_precision' => ['nullable', 'string', 'max:255'],
            'assureur_actuel' => ['nullable', 'string', 'max:255'],
            'date_echeance_actuelle' => ['nullable', 'date'],
            'motif_changement' => ['nullable', 'string', 'max:255'],
            'personnes_a_couvrir' => ['nullable', 'array'],
            'personnes_a_couvrir.*.nom' => ['nullable', 'string', 'max:255'],
            'personnes_a_couvrir.*.date_naissance' => ['nullable', 'date'],
            'personnes_a_couvrir.*.lien' => ['nullable', 'string', 'in:assure_principal,conjoint,enfant'],
            'niveaux_garanties' => ['nullable', 'array'],
            'niveaux_garanties.hospitalisation' => ['nullable', 'string', 'in:economique,standard,renforce,haut_de_gamme'],
            'niveaux_garanties.soins_courants' => ['nullable', 'string', 'in:economique,standard,renforce,haut_de_gamme'],
            'niveaux_garanties.dentaire' => ['nullable', 'string', 'in:economique,standard,renforce,haut_de_gamme'],
            'niveaux_garanties.optique' => ['nullable', 'string', 'in:economique,standard,renforce,haut_de_gamme'],
            'niveaux_garanties.audioprothese' => ['nullable', 'string', 'in:economique,standard,renforce,haut_de_gamme'],
            'niveaux_garanties.medecines_douces' => ['nullable', 'string', 'in:economique,standard,renforce,haut_de_gamme'],
            'besoins_specifiques' => ['nullable', 'array'],
            'besoins_specifiques.*' => ['string', 'in:lentilles,orthodontie,implants_dentaires,chambre_particuliere,maternite'],
            'budget_mensuel_souhaite' => ['nullable', 'numeric'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['personnes_a_couvrir'] = array_values(array_filter(
            $validated['personnes_a_couvrir'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_complementaire_sante',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-complementaire-sante', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatComplementaireSantePdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_complementaire_sante')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-complementaire-sante', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $regimeLabels = [
            'salarie' => 'Salarié',
            'tns' => 'Travailleur non salarié (TNS)',
            'fonctionnaire' => 'Fonctionnaire',
            'retraite' => 'Retraité',
            'sans_emploi' => 'Sans emploi',
            'autre' => 'Autre',
        ];
        $niveauLabels = [
            'economique' => 'Économique',
            'standard' => 'Standard',
            'renforce' => 'Renforcé',
            'haut_de_gamme' => 'Haut de gamme',
        ];
        $posteLabels = [
            'hospitalisation' => 'Hospitalisation',
            'soins_courants' => 'Soins courants',
            'dentaire' => 'Dentaire',
            'optique' => 'Optique',
            'audioprothese' => 'Audioprothèse',
            'medecines_douces' => 'Médecines douces',
        ];
        $besoinsLabels = [
            'lentilles' => 'Lentilles',
            'orthodontie' => 'Orthodontie',
            'implants_dentaires' => 'Implants dentaires',
            'chambre_particuliere' => 'Chambre particulière',
            'maternite' => 'Maternité',
        ];
        $lienLabels = [
            'assure_principal' => 'Assuré principal',
            'conjoint' => 'Conjoint',
            'enfant' => 'Enfant',
        ];

        $personnes = array_map(function ($personne) use ($lienLabels) {
            $personne['lien_libelle'] = $lienLabels[$personne['lien'] ?? ''] ?? '-';
            return $personne;
        }, $donnees['personnes_a_couvrir'] ?? []);

        $niveauxGaranties = [];
        foreach ($posteLabels as $poste => $labelPoste) {
            $valeur = $donnees['niveaux_garanties'][$poste] ?? '';
            $niveauxGaranties[$labelPoste] = $niveauLabels[$valeur] ?? 'Non prioritaire';
        }

        $recueil = [
            'regime_obligatoire_libelle' => $regimeLabels[$donnees['regime_obligatoire'] ?? ''] ?? '-',
            'regime_obligatoire_autre_precision' => $donnees['regime_obligatoire_autre_precision'] ?? null,
            'assureur_actuel' => $donnees['assureur_actuel'] ?? null,
            'date_echeance_actuelle' => $donnees['date_echeance_actuelle'] ?? null,
            'motif_changement' => $donnees['motif_changement'] ?? null,
            'personnes_a_couvrir' => $personnes,
            'niveaux_garanties' => $niveauxGaranties,
            'besoins_specifiques_libelle' => implode(', ', array_map(
                fn ($v) => $besoinsLabels[$v] ?? $v,
                $donnees['besoins_specifiques'] ?? []
            )) ?: 'Aucun',
            'budget_mensuel_souhaite' => $donnees['budget_mensuel_souhaite'] ?? null,
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-complementaire-sante',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat complémentaire santé', $client);

        return $pdf->download($filename);
    }

    public function mandatContratCapitalisation(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_contrat_capitalisation')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-contrat-capitalisation', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatContratCapitalisation(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'type_souscripteur' => ['nullable', 'string', 'in:personne_physique,personne_morale'],
            'raison_sociale_souscripteur' => ['nullable', 'string', 'max:255'],
            'siren_souscripteur' => ['nullable', 'string', 'max:20'],
            'objectifs' => ['nullable', 'array'],
            'objectifs.*' => ['string', 'in:epargne,valorisation_tresorerie,transmission_demembrement,diversification,autre'],
            'objectif_autre_precision' => ['nullable', 'string', 'max:255'],
            'horizon' => ['nullable', 'string', 'in:moins_2_ans,2_5_ans,5_8_ans,plus_8_ans'],
            'versement_initial' => ['nullable', 'numeric'],
            'versement_programme_montant' => ['nullable', 'numeric'],
            'versement_programme_periodicite' => ['nullable', 'string', 'in:mensuel,trimestriel,annuel'],
            'mode_detention' => ['nullable', 'string', 'in:pleine_propriete,demembrement'],
            'beneficiaires_demembrement' => ['nullable', 'array'],
            'beneficiaires_demembrement.*.nom' => ['nullable', 'string', 'max:255'],
            'beneficiaires_demembrement.*.lien' => ['nullable', 'string', 'max:255'],
            'beneficiaires_demembrement.*.quote_part_nue_propriete' => ['nullable', 'string', 'max:100'],
            'mode_gestion' => ['nullable', 'string', 'in:libre,pilotee,mandat'],
            'repartition_risque' => ['nullable', 'string', 'in:prudent,equilibre,dynamique'],
            'rachat_possible' => ['nullable', 'string', 'in:oui,non'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['beneficiaires_demembrement'] = array_values(array_filter(
            $validated['beneficiaires_demembrement'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_contrat_capitalisation',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-contrat-capitalisation', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatContratCapitalisationPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_contrat_capitalisation')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-contrat-capitalisation', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $souscripteurLabels = [
            'personne_physique' => 'Personne physique',
            'personne_morale' => 'Personne morale',
        ];
        $objectifsLabels = [
            'epargne' => 'Épargne à moyen/long terme',
            'valorisation_tresorerie' => 'Valorisation de trésorerie',
            'transmission_demembrement' => 'Transmission en démembrement',
            'diversification' => 'Diversification',
            'autre' => 'Autre',
        ];
        $horizonLabels = [
            'moins_2_ans' => 'Moins de 2 ans',
            '2_5_ans' => '2 à 5 ans',
            '5_8_ans' => '5 à 8 ans',
            'plus_8_ans' => 'Plus de 8 ans',
        ];
        $periodiciteLabels = ['mensuel' => 'Mensuel', 'trimestriel' => 'Trimestriel', 'annuel' => 'Annuel'];
        $detentionLabels = [
            'pleine_propriete' => 'Pleine propriété',
            'demembrement' => 'Démembrement (usufruit / nue-propriété)',
        ];
        $modeGestionLabels = ['libre' => 'Gestion libre', 'pilotee' => 'Gestion pilotée', 'mandat' => 'Gestion sous mandat'];
        $repartitionLabels = ['prudent' => 'Prudent', 'equilibre' => 'Équilibré', 'dynamique' => 'Dynamique'];
        $ouiNon = fn ($valeur) => $valeur === 'oui' ? 'Oui' : ($valeur === 'non' ? 'Non' : '-');

        $recueil = [
            'type_souscripteur_libelle' => $souscripteurLabels[$donnees['type_souscripteur'] ?? ''] ?? '-',
            'raison_sociale_souscripteur' => $donnees['raison_sociale_souscripteur'] ?? null,
            'siren_souscripteur' => $donnees['siren_souscripteur'] ?? null,
            'objectifs_libelle' => implode(', ', array_map(
                fn ($v) => $objectifsLabels[$v] ?? $v,
                $donnees['objectifs'] ?? []
            )) ?: '-',
            'objectif_autre_precision' => $donnees['objectif_autre_precision'] ?? null,
            'horizon_libelle' => $horizonLabels[$donnees['horizon'] ?? ''] ?? '-',
            'versement_initial' => $donnees['versement_initial'] ?? null,
            'versement_programme_montant' => $donnees['versement_programme_montant'] ?? null,
            'versement_programme_periodicite_libelle' => $periodiciteLabels[$donnees['versement_programme_periodicite'] ?? ''] ?? '-',
            'mode_detention_libelle' => $detentionLabels[$donnees['mode_detention'] ?? ''] ?? '-',
            'beneficiaires_demembrement' => $donnees['beneficiaires_demembrement'] ?? [],
            'mode_gestion_libelle' => $modeGestionLabels[$donnees['mode_gestion'] ?? ''] ?? '-',
            'repartition_risque_libelle' => $repartitionLabels[$donnees['repartition_risque'] ?? ''] ?? '-',
            'rachat_possible_libelle' => $ouiNon($donnees['rachat_possible'] ?? ''),
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-contrat-capitalisation',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat contrat de capitalisation', $client);

        return $pdf->download($filename);
    }

    public function mandatGarantieAccidentVie(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_garantie_accident_vie')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-garantie-accident-vie', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatGarantieAccidentVie(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'personnes_a_couvrir' => ['nullable', 'array'],
            'personnes_a_couvrir.*.nom' => ['nullable', 'string', 'max:255'],
            'personnes_a_couvrir.*.date_naissance' => ['nullable', 'date'],
            'personnes_a_couvrir.*.lien' => ['nullable', 'string', 'in:assure_principal,conjoint,enfant'],
            'activites_a_risque' => ['nullable', 'array'],
            'activites_a_risque.*' => ['string', 'in:sport_loisir,benevolat,bricolage_jardinage,sport_competition,autre'],
            'activite_autre_precision' => ['nullable', 'string', 'max:255'],
            'garanties_souhaitees' => ['nullable', 'array'],
            'garanties_souhaitees.*' => ['string', 'in:accident_vie_privee,accident_domestique,catastrophe_naturelle,agression,attentat,accident_medical'],
            'seuil_intervention' => ['nullable', 'string', 'in:5,10,20,peu_importe'],
            'capital_invalidite_totale' => ['nullable', 'numeric'],
            'statut_professionnel' => ['nullable', 'string', 'in:salarie,independant,fonctionnaire,retraite,sans_emploi'],
            'revenu_annuel' => ['nullable', 'numeric'],
            'assureur_actuel' => ['nullable', 'string', 'max:255'],
            'date_echeance_actuelle' => ['nullable', 'date'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['personnes_a_couvrir'] = array_values(array_filter(
            $validated['personnes_a_couvrir'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_garantie_accident_vie',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-garantie-accident-vie', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatGarantieAccidentViePdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_garantie_accident_vie')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-garantie-accident-vie', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $lienLabels = [
            'assure_principal' => 'Assuré principal',
            'conjoint' => 'Conjoint',
            'enfant' => 'Enfant',
        ];
        $activitesLabels = [
            'sport_loisir' => 'Sport de loisir',
            'benevolat' => 'Bénévolat associatif',
            'bricolage_jardinage' => 'Bricolage / jardinage',
            'sport_competition' => 'Sport en compétition',
            'autre' => 'Autre',
        ];
        $garantiesLabels = [
            'accident_vie_privee' => 'Accident de la vie privée',
            'accident_domestique' => 'Accident domestique',
            'catastrophe_naturelle' => 'Catastrophe naturelle ou technologique',
            'agression' => 'Agression',
            'attentat' => 'Attentat',
            'accident_medical' => 'Accident médical',
        ];
        $seuilLabels = ['5' => '5 %', '10' => '10 %', '20' => '20 %', 'peu_importe' => 'Peu importe'];
        $statutLabels = [
            'salarie' => 'Salarié',
            'independant' => 'Indépendant',
            'fonctionnaire' => 'Fonctionnaire',
            'retraite' => 'Retraité',
            'sans_emploi' => 'Sans emploi',
        ];

        $personnes = array_map(function ($personne) use ($lienLabels) {
            $personne['lien_libelle'] = $lienLabels[$personne['lien'] ?? ''] ?? '-';
            return $personne;
        }, $donnees['personnes_a_couvrir'] ?? []);

        $recueil = [
            'personnes_a_couvrir' => $personnes,
            'activites_a_risque_libelle' => implode(', ', array_map(
                fn ($v) => $activitesLabels[$v] ?? $v,
                $donnees['activites_a_risque'] ?? []
            )) ?: 'Aucune',
            'activite_autre_precision' => $donnees['activite_autre_precision'] ?? null,
            'garanties_souhaitees_libelle' => implode(', ', array_map(
                fn ($v) => $garantiesLabels[$v] ?? $v,
                $donnees['garanties_souhaitees'] ?? []
            )) ?: '-',
            'seuil_intervention_libelle' => $seuilLabels[$donnees['seuil_intervention'] ?? ''] ?? '-',
            'capital_invalidite_totale' => $donnees['capital_invalidite_totale'] ?? null,
            'statut_professionnel_libelle' => $statutLabels[$donnees['statut_professionnel'] ?? ''] ?? '-',
            'revenu_annuel' => $donnees['revenu_annuel'] ?? null,
            'assureur_actuel' => $donnees['assureur_actuel'] ?? null,
            'date_echeance_actuelle' => $donnees['date_echeance_actuelle'] ?? null,
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-garantie-accident-vie',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat garantie accident de la vie', $client);

        return $pdf->download($filename);
    }

    public function mandatAssuranceVehicule(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_vehicule')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-assurance-vehicule', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatAssuranceVehicule(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'immatriculation' => ['nullable', 'string', 'max:20'],
            'marque' => ['nullable', 'string', 'max:255'],
            'modele' => ['nullable', 'string', 'max:255'],
            'date_premiere_immatriculation' => ['nullable', 'date'],
            'genre_vehicule' => ['nullable', 'string', 'in:vp,vu,moto,autre'],
            'energie' => ['nullable', 'string', 'in:essence,diesel,electrique,hybride'],
            'valeur_vehicule' => ['nullable', 'numeric'],
            'kilometrage_actuel' => ['nullable', 'integer'],
            'usage_vehicule' => ['nullable', 'string', 'in:prive,trajet_domicile_travail,professionnel,mixte'],
            'stationnement' => ['nullable', 'string', 'in:garage_ferme,parking_prive,voie_publique'],
            'conducteurs' => ['nullable', 'array'],
            'conducteurs.*.nom' => ['nullable', 'string', 'max:255'],
            'conducteurs.*.date_naissance' => ['nullable', 'date'],
            'conducteurs.*.date_permis' => ['nullable', 'date'],
            'conducteurs.*.statut' => ['nullable', 'string', 'in:principal,secondaire'],
            'bonus_malus' => ['nullable', 'numeric'],
            'nombre_sinistres_responsables_3ans' => ['nullable', 'integer'],
            'resiliation_anterieure' => ['nullable', 'string', 'in:oui,non'],
            'resiliation_anterieure_precision' => ['nullable', 'string', 'max:255'],
            'garanties_souhaitees' => ['nullable', 'array'],
            'garanties_souhaitees.*' => ['string', 'in:tiers,tiers_etendu,tous_risques,bris_de_glace,vol_incendie,assistance_0km,protection_juridique,garantie_conducteur,vehicule_remplacement'],
            'franchise_souhaitee' => ['nullable', 'string', 'in:sans_franchise,franchise_reduite,franchise_standard,franchise_elevee'],
            'assureur_actuel' => ['nullable', 'string', 'max:255'],
            'date_echeance_actuelle' => ['nullable', 'date'],
            'resiliation_hamon' => ['nullable', 'string', 'in:oui,non'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['conducteurs'] = array_values(array_filter(
            $validated['conducteurs'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_assurance_vehicule',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-assurance-vehicule', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatAssuranceVehiculePdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_vehicule')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-assurance-vehicule', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $genreLabels = [
            'vp' => 'Véhicule particulier (VP)',
            'vu' => 'Véhicule utilitaire (VU)',
            'moto' => 'Moto / scooter',
            'autre' => 'Autre',
        ];
        $energieLabels = [
            'essence' => 'Essence',
            'diesel' => 'Diesel',
            'electrique' => 'Électrique',
            'hybride' => 'Hybride',
        ];
        $usageLabels = [
            'prive' => 'Privé uniquement',
            'trajet_domicile_travail' => 'Privé et trajet domicile-travail',
            'professionnel' => 'Professionnel',
            'mixte' => 'Mixte privé et professionnel',
        ];
        $stationnementLabels = [
            'garage_ferme' => 'Garage fermé',
            'parking_prive' => 'Parking privé non fermé',
            'voie_publique' => 'Voie publique',
        ];
        $statutConducteurLabels = [
            'principal' => 'Conducteur principal',
            'secondaire' => 'Conducteur secondaire',
        ];
        $garantiesLabels = [
            'tiers' => 'Au tiers',
            'tiers_etendu' => 'Tiers étendu',
            'tous_risques' => 'Tous risques',
            'bris_de_glace' => 'Bris de glace',
            'vol_incendie' => 'Vol et incendie',
            'assistance_0km' => 'Assistance 0 km',
            'protection_juridique' => 'Protection juridique',
            'garantie_conducteur' => 'Garantie du conducteur',
            'vehicule_remplacement' => 'Véhicule de remplacement',
        ];
        $franchiseLabels = [
            'sans_franchise' => 'Sans franchise',
            'franchise_reduite' => 'Franchise réduite',
            'franchise_standard' => 'Franchise standard',
            'franchise_elevee' => 'Franchise élevée',
        ];
        $ouiNon = fn ($valeur) => $valeur === 'oui' ? 'Oui' : ($valeur === 'non' ? 'Non' : '-');

        $conducteurs = array_map(function ($conducteur) use ($statutConducteurLabels) {
            $conducteur['statut_libelle'] = $statutConducteurLabels[$conducteur['statut'] ?? ''] ?? '-';
            return $conducteur;
        }, $donnees['conducteurs'] ?? []);

        $recueil = [
            'immatriculation' => $donnees['immatriculation'] ?? null,
            'marque' => $donnees['marque'] ?? null,
            'modele' => $donnees['modele'] ?? null,
            'date_premiere_immatriculation' => $donnees['date_premiere_immatriculation'] ?? null,
            'genre_vehicule_libelle' => $genreLabels[$donnees['genre_vehicule'] ?? ''] ?? '-',
            'energie_libelle' => $energieLabels[$donnees['energie'] ?? ''] ?? '-',
            'valeur_vehicule' => $donnees['valeur_vehicule'] ?? null,
            'kilometrage_actuel' => $donnees['kilometrage_actuel'] ?? null,
            'usage_vehicule_libelle' => $usageLabels[$donnees['usage_vehicule'] ?? ''] ?? '-',
            'stationnement_libelle' => $stationnementLabels[$donnees['stationnement'] ?? ''] ?? '-',
            'conducteurs' => $conducteurs,
            'bonus_malus' => $donnees['bonus_malus'] ?? null,
            'nombre_sinistres_responsables_3ans' => $donnees['nombre_sinistres_responsables_3ans'] ?? null,
            'resiliation_anterieure_libelle' => $ouiNon($donnees['resiliation_anterieure'] ?? ''),
            'resiliation_anterieure_precision' => $donnees['resiliation_anterieure_precision'] ?? null,
            'garanties_souhaitees_libelle' => implode(', ', array_map(
                fn ($v) => $garantiesLabels[$v] ?? $v,
                $donnees['garanties_souhaitees'] ?? []
            )) ?: '-',
            'franchise_souhaitee_libelle' => $franchiseLabels[$donnees['franchise_souhaitee'] ?? ''] ?? '-',
            'assureur_actuel' => $donnees['assureur_actuel'] ?? null,
            'date_echeance_actuelle' => $donnees['date_echeance_actuelle'] ?? null,
            'resiliation_hamon_libelle' => $ouiNon($donnees['resiliation_hamon'] ?? ''),
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-assurance-vehicule',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat assurance véhicule', $client);

        return $pdf->download($filename);
    }

    public function mandatPlanEpargneRetraite(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_plan_epargne_retraite')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-plan-epargne-retraite', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatPlanEpargneRetraite(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'statut_professionnel' => ['nullable', 'string', 'in:salarie,tns,independant,fonctionnaire'],
            'tmi_actuelle' => ['nullable', 'string', 'in:0,11,30,41,45'],
            'deduction_fiscale' => ['nullable', 'string', 'in:deduire,ne_pas_deduire'],
            'objectifs' => ['nullable', 'array'],
            'objectifs.*' => ['string', 'in:complement_revenu_retraite,reduction_impot,transmission,autre'],
            'objectif_autre_precision' => ['nullable', 'string', 'max:255'],
            'age_actuel' => ['nullable', 'integer'],
            'age_retraite_envisage' => ['nullable', 'integer'],
            'versement_initial' => ['nullable', 'numeric'],
            'versement_programme_montant' => ['nullable', 'numeric'],
            'versement_programme_periodicite' => ['nullable', 'string', 'in:mensuel,trimestriel,annuel'],
            'transferts_contrats' => ['nullable', 'array'],
            'transferts_contrats.*.type_contrat' => ['nullable', 'string', 'in:perp,madelin,article_83,percol,per_individuel,autre'],
            'transferts_contrats.*.organisme' => ['nullable', 'string', 'max:255'],
            'transferts_contrats.*.valeur_transferee' => ['nullable', 'numeric'],
            'mode_gestion' => ['nullable', 'string', 'in:gestion_pilotee_horizon,gestion_libre'],
            'profil_allocation' => ['nullable', 'string', 'in:prudent,equilibre,dynamique'],
            'modalite_sortie' => ['nullable', 'string', 'in:capital,rente,mixte'],
            'beneficiaires_deces' => ['nullable', 'array'],
            'beneficiaires_deces.*.nom' => ['nullable', 'string', 'max:255'],
            'beneficiaires_deces.*.lien' => ['nullable', 'string', 'max:255'],
            'beneficiaires_deces.*.quote_part' => ['nullable', 'string', 'max:100'],
            'clause_beneficiaire_type' => ['nullable', 'string', 'in:standard,personnalisee'],
            'clause_beneficiaire_texte' => ['nullable', 'string', 'max:2000'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['transferts_contrats'] = array_values(array_filter(
            $validated['transferts_contrats'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));
        $validated['beneficiaires_deces'] = array_values(array_filter(
            $validated['beneficiaires_deces'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_plan_epargne_retraite',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-plan-epargne-retraite', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatPlanEpargneRetraitePdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_plan_epargne_retraite')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-plan-epargne-retraite', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $statutLabels = [
            'salarie' => 'Salarié',
            'tns' => 'Travailleur non salarié (TNS)',
            'independant' => 'Indépendant',
            'fonctionnaire' => 'Fonctionnaire',
        ];
        $tmiLabels = ['0' => '0 %', '11' => '11 %', '30' => '30 %', '41' => '41 %', '45' => '45 %'];
        $deductionLabels = [
            'deduire' => 'Déduire les versements du revenu imposable',
            'ne_pas_deduire' => 'Ne pas déduire (sortie moins fiscalisée)',
        ];
        $objectifsLabels = [
            'complement_revenu_retraite' => 'Complément de revenu à la retraite',
            'reduction_impot' => "Réduction d'impôt immédiate",
            'transmission' => 'Transmission',
            'autre' => 'Autre',
        ];
        $periodiciteLabels = ['mensuel' => 'Mensuel', 'trimestriel' => 'Trimestriel', 'annuel' => 'Annuel'];
        $typeContratTransfertLabels = [
            'perp' => 'PERP',
            'madelin' => 'Contrat Madelin',
            'article_83' => 'Article 83',
            'percol' => 'PER collectif (PERCOL / PERCO)',
            'per_individuel' => 'PER individuel existant',
            'autre' => 'Autre',
        ];
        $modeGestionLabels = [
            'gestion_pilotee_horizon' => 'Gestion pilotée à horizon (par défaut)',
            'gestion_libre' => 'Gestion libre',
        ];
        $profilAllocationLabels = ['prudent' => 'Prudent', 'equilibre' => 'Équilibré', 'dynamique' => 'Dynamique'];
        $modaliteSortieLabels = [
            'capital' => 'Capital',
            'rente' => 'Rente viagère',
            'mixte' => 'Mixte (capital et rente)',
        ];

        $clauseTexte = ($donnees['clause_beneficiaire_type'] ?? 'standard') === 'personnalisee'
            ? ($donnees['clause_beneficiaire_texte'] ?? '')
            : 'Mon conjoint, à défaut mes enfants nés ou à naître, vivants ou représentés, à défaut mes héritiers.';

        $transferts = array_map(function ($transfert) use ($typeContratTransfertLabels) {
            $transfert['type_contrat_libelle'] = $typeContratTransfertLabels[$transfert['type_contrat'] ?? ''] ?? '-';
            return $transfert;
        }, $donnees['transferts_contrats'] ?? []);

        $recueil = [
            'statut_professionnel_libelle' => $statutLabels[$donnees['statut_professionnel'] ?? ''] ?? '-',
            'tmi_actuelle_libelle' => $tmiLabels[$donnees['tmi_actuelle'] ?? ''] ?? '-',
            'deduction_fiscale_libelle' => $deductionLabels[$donnees['deduction_fiscale'] ?? ''] ?? '-',
            'objectifs_libelle' => implode(', ', array_map(
                fn ($v) => $objectifsLabels[$v] ?? $v,
                $donnees['objectifs'] ?? []
            )) ?: '-',
            'objectif_autre_precision' => $donnees['objectif_autre_precision'] ?? null,
            'age_actuel' => $donnees['age_actuel'] ?? null,
            'age_retraite_envisage' => $donnees['age_retraite_envisage'] ?? null,
            'versement_initial' => $donnees['versement_initial'] ?? null,
            'versement_programme_montant' => $donnees['versement_programme_montant'] ?? null,
            'versement_programme_periodicite_libelle' => $periodiciteLabels[$donnees['versement_programme_periodicite'] ?? ''] ?? '-',
            'transferts_contrats' => $transferts,
            'mode_gestion_libelle' => $modeGestionLabels[$donnees['mode_gestion'] ?? ''] ?? '-',
            'profil_allocation_libelle' => $profilAllocationLabels[$donnees['profil_allocation'] ?? ''] ?? '-',
            'modalite_sortie_libelle' => $modaliteSortieLabels[$donnees['modalite_sortie'] ?? ''] ?? '-',
            'beneficiaires_deces' => $donnees['beneficiaires_deces'] ?? [],
            'clause_beneficiaire_texte_finale' => $clauseTexte,
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.mandat-plan-epargne-retraite',
            $data
        );

        $filename = $this->nommerFichierPdf('Mandat plan d\'épargne retraite', $client);

        return $pdf->download($filename);
    }

    public function lettreMissionScpi(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'lettre_mission_scpi')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.lettre-mission-scpi', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerLettreMissionScpi(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'objectifs' => ['nullable', 'array'],
            'objectifs.*' => ['string', 'in:complement_revenu,valorisation_capital,diversification_patrimoine,reduction_fiscale,transmission,autre'],
            'objectif_autre_precision' => ['nullable', 'string', 'max:255'],
            'horizon_investissement' => ['nullable', 'string', 'in:moins_5_ans,5_10_ans,10_15_ans,plus_15_ans'],
            'montant_investissement_envisage' => ['nullable', 'numeric'],
            'mode_financement' => ['nullable', 'string', 'in:comptant,credit,demembrement'],
            'montant_credit_envisage' => ['nullable', 'numeric'],
            'duree_credit_envisagee' => ['nullable', 'integer'],
            'mode_detention' => ['nullable', 'string', 'in:direct,assurance_vie,compte_titres,societe'],
            'type_scpi_recherche' => ['nullable', 'string', 'in:rendement,fiscale,europeenne,plus_value,pas_de_preference'],
            'tmi_actuelle' => ['nullable', 'string', 'in:0,11,30,41,45'],
            'scpi_deja_detenues' => ['nullable', 'array'],
            'scpi_deja_detenues.*.nom_scpi' => ['nullable', 'string', 'max:255'],
            'scpi_deja_detenues.*.nombre_parts' => ['nullable', 'integer'],
            'scpi_deja_detenues.*.montant_detenu' => ['nullable', 'numeric'],
            'patrimoine_financier_existant' => ['nullable', 'numeric'],
            'revenu_annuel_foyer' => ['nullable', 'numeric'],
            'attentes_liquidite' => ['nullable', 'string', 'in:importante,secondaire,non_prioritaire'],
            'risques_pris_connaissance' => ['nullable', 'array'],
            'risques_pris_connaissance.*' => ['string', 'in:absence_garantie_capital,absence_garantie_revenu,duree_detention_recommandee,risque_liquidite,fluctuation_valeur_part'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['scpi_deja_detenues'] = array_values(array_filter(
            $validated['scpi_deja_detenues'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'lettre_mission_scpi',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.lettre-mission-scpi', $client)
            ->with('status', 'Lettre de mission enregistrée.');
    }

    public function telechargerLettreMissionScpiPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'lettre_mission_scpi')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.lettre-mission-scpi', $client)
                ->with('error', 'Aucune lettre de mission enregistrée à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $objectifsLabels = [
            'complement_revenu' => 'Complément de revenu',
            'valorisation_capital' => 'Valorisation du capital',
            'diversification_patrimoine' => 'Diversification du patrimoine',
            'reduction_fiscale' => 'Réduction fiscale',
            'transmission' => 'Transmission',
            'autre' => 'Autre',
        ];
        $horizonLabels = [
            'moins_5_ans' => 'Moins de 5 ans',
            '5_10_ans' => '5 à 10 ans',
            '10_15_ans' => '10 à 15 ans',
            'plus_15_ans' => 'Plus de 15 ans',
        ];
        $modeFinancementLabels = ['comptant' => 'Comptant', 'credit' => 'À crédit', 'demembrement' => 'Démembrement'];
        $modeDetentionLabels = [
            'direct' => 'Direct (nom propre)',
            'assurance_vie' => 'Via assurance vie',
            'compte_titres' => 'Via compte titres',
            'societe' => 'Via société (SCI, holding...)',
        ];
        $typeScpiLabels = [
            'rendement' => 'SCPI de rendement',
            'fiscale' => 'SCPI fiscale',
            'europeenne' => 'SCPI européenne',
            'plus_value' => 'SCPI de plus-value',
            'pas_de_preference' => 'Pas de préférence',
        ];
        $tmiLabels = ['0' => '0 %', '11' => '11 %', '30' => '30 %', '41' => '41 %', '45' => '45 %'];
        $liquiditeLabels = [
            'importante' => 'Importante',
            'secondaire' => 'Secondaire',
            'non_prioritaire' => 'Non prioritaire',
        ];
        $risquesLabels = [
            'absence_garantie_capital' => 'Absence de garantie du capital investi',
            'absence_garantie_revenu' => 'Absence de garantie sur le niveau des revenus distribués',
            'duree_detention_recommandee' => 'Durée de détention recommandée (long terme)',
            'risque_liquidite' => 'Risque de liquidité (revente des parts non garantie)',
            'fluctuation_valeur_part' => 'Fluctuation de la valeur de la part dans le temps',
        ];

        $recueil = [
            'objectifs_libelle' => implode(', ', array_map(
                fn ($v) => $objectifsLabels[$v] ?? $v,
                $donnees['objectifs'] ?? []
            )) ?: '-',
            'objectif_autre_precision' => $donnees['objectif_autre_precision'] ?? null,
            'horizon_investissement_libelle' => $horizonLabels[$donnees['horizon_investissement'] ?? ''] ?? '-',
            'montant_investissement_envisage' => $donnees['montant_investissement_envisage'] ?? null,
            'mode_financement_libelle' => $modeFinancementLabels[$donnees['mode_financement'] ?? ''] ?? '-',
            'montant_credit_envisage' => $donnees['montant_credit_envisage'] ?? null,
            'duree_credit_envisagee' => $donnees['duree_credit_envisagee'] ?? null,
            'mode_detention_libelle' => $modeDetentionLabels[$donnees['mode_detention'] ?? ''] ?? '-',
            'type_scpi_recherche_libelle' => $typeScpiLabels[$donnees['type_scpi_recherche'] ?? ''] ?? '-',
            'tmi_actuelle_libelle' => $tmiLabels[$donnees['tmi_actuelle'] ?? ''] ?? '-',
            'scpi_deja_detenues' => $donnees['scpi_deja_detenues'] ?? [],
            'patrimoine_financier_existant' => $donnees['patrimoine_financier_existant'] ?? null,
            'revenu_annuel_foyer' => $donnees['revenu_annuel_foyer'] ?? null,
            'attentes_liquidite_libelle' => $liquiditeLabels[$donnees['attentes_liquidite'] ?? ''] ?? '-',
            'risques_pris_connaissance_libelle' => implode(', ', array_map(
                fn ($v) => $risquesLabels[$v] ?? $v,
                $donnees['risques_pris_connaissance'] ?? []
            )) ?: '-',
            'commentaire_conseiller' => $donnees['commentaire_conseiller'] ?? null,
        ];

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
            'recueil' => $recueil,
            'donnees' => $donnees,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.lettre-mission-scpi',
            $data
        );

        $filename = $this->nommerFichierPdf('Lettre de mission SCPI', $client);

        return $pdf->download($filename);
    }

    public function planAction(
        Client $client
    ): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        $recommandation = $client->analyses()
            ->where('type', 'recommandation')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();
        if (! $recommandation) {
            return redirect()
                ->route('tenant.clients.recommandation-patrimoniale', $client)
                ->with(
                    'error',
                    'Le plan d\'action nécessite une recommandation patrimoniale générée au préalable.'
                );
        }
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $dernierPlanAction = $client->analyses()
            ->where('type', 'plan_action')
            ->latest('created_at')
            ->first();
        return view(
            'tenant.clients.plan-action',
            [
                'client' => $client,
                'cabinet' => $cabinet,
                'planAction' => $dernierPlanAction,
            ]
        );
    }

    public function genererPlanAction(
        \Illuminate\Http\Request $request,
        Client $client,
        \App\Services\AI\PlanActionAnalysisService $planActionAnalysis
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'contexte' => ['nullable', 'string', 'max:5000'],
        ]);

        try {
            $planActionAnalysis->analyze($client, [
                'contexte' => $validated['contexte'] ?? '',
            ]);
            return redirect()
                ->route('tenant.clients.plan-action', $client)
                ->with('status', 'Plan d\'action généré.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                'Erreur génération plan d\'action',
                [
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]
            );
            return redirect()
                ->route('tenant.clients.plan-action', $client)
                ->with('error', "Le plan d'action n'a pas pu être généré.");
        }
    }

    public function telechargerPlanActionPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $planAction = $client->analyses()
            ->where('type', 'plan_action')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $planAction) {
            return redirect()
                ->route('tenant.clients.plan-action', $client)
                ->with('error', 'Aucun plan d\'action généré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $corpsHtml = $planAction->result_json['plan_action_html']
            ?? \App\Services\AI\RecommandationAnalysisService::convertirMarkdownEnHtml(
                $planAction->result_json['plan_action'] ?? $planAction->raw_response ?? ''
            );

        $data = [
            'client' => $client,
            'cabinet' => $cabinet,
            'planAction' => $planAction,
            'nomClient' => $nomClient,
            'nomConseiller' => $conseiller?->name ?? auth()->user()->name,
            'telConseiller' => $conseiller?->telephone_mobile,
            'mailConseiller' => $conseiller?->email,
            'lieuSignature' => $request->query('lieu') ?: ($client->kyc?->lieu_signature ?: $cabinet?->ville),
            'dateGeneration' => now()->translatedFormat('d F Y'),
            'corpsHtml' => $corpsHtml,
            'fontRegular' => base_path('resources/fonts/Montserrat-Regular.ttf'),
            'fontMedium' => base_path('resources/fonts/Montserrat-Medium.ttf'),
            'fontSemiBold' => base_path('resources/fonts/Montserrat-SemiBold.ttf'),
            'fontBold' => base_path('resources/fonts/Montserrat-Bold.ttf'),
            'logoPath' => $cabinet?->logo ? storage_path('app/public/' . $cabinet->logo) : null,
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant.clients.pdf.plan-action',
            $data
        );

        $filename = $this->nommerFichierPdf('Plan d\'action', $client);

        return $pdf->download($filename);
    }

    public function modifierPlanActionContenu(
        \Illuminate\Http\Request $request,
        Client $client,
        \App\Models\ClientAnalysis $analysis
    ): \Illuminate\Http\RedirectResponse
    {
        abort_unless($analysis->client_id === $client->id, 404);
        abort_unless($analysis->type === 'plan_action', 404);
        $validated = $request->validate([
            'contenu_html' => ['required', 'string'],
        ]);
        $contenuNettoye = strip_tags(
            $validated['contenu_html'],
            '<h2><span><p><strong><br><ul><ol><li><em>'
        );
        $resultJson = $analysis->result_json ?? [];
        $resultJson['plan_action_html'] = $contenuNettoye;
        $analysis->update(['result_json' => $resultJson]);
        return redirect()
            ->route('tenant.clients.plan-action', $client)
            ->with('status', 'Modifications enregistrées.');
    }

    public function genererSuggestion(
        Client $client,
        \App\Services\AI\SuggestionAnalysisService $suggestionAnalysis
    ): \Illuminate\Http\RedirectResponse
    {
        $analyses = $client->analyses()
            ->where('status', 'completed')
            ->whereIn('type', [
                'kyc',
                'patrimoine',
                'profil_investisseur',
            ])
            ->whereNotNull('completed_at')
            ->where(
                'completed_at',
                '>=',
                now()->subYear()
            )
            ->latest('completed_at')
            ->get()
            ->groupBy('type')
            ->map(
                fn ($items) => $items->first()
            );

        $typesRequis = [
            'kyc',
            'patrimoine',
            'profil_investisseur',
        ];

        foreach ($typesRequis as $type) {

            if (! $analyses->has($type)) {

                return redirect()
                    ->route(
                        'tenant.clients.aide-decision',
                        $client
                    )
                    ->with(
                        'error',
                        'La suggestion nécessite les trois analyses du dossier, réalisées depuis moins d’un an.'
                    );
            }
        }

        try {

            $suggestion = $suggestionAnalysis->analyze($client);

            return redirect()
                ->route(
                    'tenant.clients.aide-decision',
                    $client
                )
                ->with(
                    'status',
                    'Suggestion de prestations générée.'
                );

        } catch (\Throwable $e) {

            \Illuminate\Support\Facades\Log::error(
                'Erreur génération suggestion prestations',
                [
                    'client_id' => $client->id,
                    'error' => $e->getMessage(),
                ]
            );

            return redirect()
                ->route(
                    'tenant.clients.aide-decision',
                    $client
                )
                ->with(
                    'error',
                    'La suggestion n’a pas pu être générée. Les analyses du dossier restent inchangées.'
                );
        }
    }



}
