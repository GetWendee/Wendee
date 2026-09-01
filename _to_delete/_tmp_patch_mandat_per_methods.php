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

        $filename = 'Mandat-Plan-Epargne-Retraite-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

