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

        $filename = 'Lettre-Mission-SCPI-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

