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

        $filename = 'Mandat-Complementaire-Sante-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

