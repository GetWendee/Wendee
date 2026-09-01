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

        $filename = 'Mandat-Contrat-Capitalisation-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

