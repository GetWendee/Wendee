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

        $filename = 'Mandat-Assurance-Emprunteur-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

