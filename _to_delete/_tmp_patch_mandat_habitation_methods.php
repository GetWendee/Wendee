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

        $filename = 'Mandat-Assurance-Habitation-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

