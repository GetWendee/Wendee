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
            'date_naissance' => ['nullable', 'date'],
            'telephone_mobile' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]{10}$/'],
            'telephone_domicile' => ['nullable', 'string', 'max:30'],
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
            'date_naissance' => ['nullable', 'date'],
            'telephone_mobile' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]{10}$/'],
            'telephone_domicile' => ['nullable', 'string', 'max:30'],
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
                'in:courtage_assurance_banque,conseil_investissement_financier,conseil_investissement_immobilier',
            ],
            'montants' => ['nullable', 'array'],
            'montants.*' => ['nullable', 'numeric'],
            'taux' => ['nullable', 'array'],
            'taux.*' => ['nullable', 'numeric'],
        ]);

        $missionLabels = [
            'courtage_assurance_banque' => 'Mandat de courtage, assurance banque',
            'conseil_investissement_financier' => 'Conseils en investissement financier',
            'conseil_investissement_immobilier' => 'Conseils en investissement immobilier',
        ];
        $missionIndex = [
            'courtage_assurance_banque' => 0,
            'conseil_investissement_financier' => 1,
            'conseil_investissement_immobilier' => 2,
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

        $filename = 'Recommandation-Patrimoniale-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

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

        $filename = 'Mandat-Assurance-Vie-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

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

        $filename = 'Plan-Action-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

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
