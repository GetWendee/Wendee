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

        $filename = 'Mandat-Garantie-Accident-Vie-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

