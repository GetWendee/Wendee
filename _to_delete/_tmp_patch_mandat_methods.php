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
