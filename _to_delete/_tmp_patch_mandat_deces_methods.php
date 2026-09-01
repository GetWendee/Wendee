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

        $filename = 'Mandat-Assurance-Deces-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

