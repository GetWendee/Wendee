    public function mandatAssuranceObseques(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_obseques')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-assurance-obseques', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatAssuranceObseques(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'objectif_contrat' => ['nullable', 'string', 'in:financer_obseques,financer_obseques_plus_transmission'],
            'type_contrat' => ['nullable', 'string', 'in:capital_deces,prestations'],
            'capital_souhaite' => ['nullable', 'numeric'],
            'modalite_versement' => ['nullable', 'string', 'in:unique,periodique'],
            'montant_versement_periodique' => ['nullable', 'numeric'],
            'periodicite_versement' => ['nullable', 'string', 'in:mensuel,trimestriel,annuel'],
            'type_ceremonie' => ['nullable', 'string', 'in:inhumation,cremation,indifferent'],
            'lieu_souhaite' => ['nullable', 'string', 'max:255'],
            'prestataire_pressenti' => ['nullable', 'string', 'max:255'],
            'precisions_obseques' => ['nullable', 'string', 'max:2000'],
            'organisateurs' => ['nullable', 'array'],
            'organisateurs.*.nom' => ['nullable', 'string', 'max:255'],
            'organisateurs.*.lien' => ['nullable', 'string', 'max:255'],
            'organisateurs.*.telephone' => ['nullable', 'string', 'max:30'],
            'age_assure' => ['nullable', 'integer'],
            'affection_grave_declaree' => ['nullable', 'string', 'in:oui,non'],
            'affection_grave_precision' => ['nullable', 'string', 'max:2000'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['organisateurs'] = array_values(array_filter(
            $validated['organisateurs'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_assurance_obseques',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-assurance-obseques', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatAssuranceObsequesPdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_obseques')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-assurance-obseques', $client)
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
            'financer_obseques' => 'Financer uniquement les obsèques',
            'financer_obseques_plus_transmission' => 'Financer les obsèques et transmettre un reliquat',
        ];
        $typeContratLabels = [
            'capital_deces' => 'Capital décès simple',
            'prestations' => 'Contrat en prestations (organisation prise en charge)',
        ];
        $modaliteLabels = [
            'unique' => 'Versement unique',
            'periodique' => 'Versements périodiques',
        ];
        $periodiciteLabels = ['mensuel' => 'Mensuel', 'trimestriel' => 'Trimestriel', 'annuel' => 'Annuel'];
        $typeCeremonieLabels = [
            'inhumation' => 'Inhumation',
            'cremation' => 'Crémation',
            'indifferent' => 'Indifférent',
        ];
        $ouiNon = fn ($valeur) => $valeur === 'oui' ? 'Oui' : ($valeur === 'non' ? 'Non' : '-');

        $recueil = [
            'objectif_contrat_libelle' => $objectifLabels[$donnees['objectif_contrat'] ?? ''] ?? '-',
            'type_contrat_libelle' => $typeContratLabels[$donnees['type_contrat'] ?? ''] ?? '-',
            'capital_souhaite' => $donnees['capital_souhaite'] ?? null,
            'modalite_versement_libelle' => $modaliteLabels[$donnees['modalite_versement'] ?? ''] ?? '-',
            'montant_versement_periodique' => $donnees['montant_versement_periodique'] ?? null,
            'periodicite_versement_libelle' => $periodiciteLabels[$donnees['periodicite_versement'] ?? ''] ?? '-',
            'type_ceremonie_libelle' => $typeCeremonieLabels[$donnees['type_ceremonie'] ?? ''] ?? '-',
            'lieu_souhaite' => $donnees['lieu_souhaite'] ?? null,
            'prestataire_pressenti' => $donnees['prestataire_pressenti'] ?? null,
            'precisions_obseques' => $donnees['precisions_obseques'] ?? null,
            'organisateurs' => $donnees['organisateurs'] ?? [],
            'age_assure' => $donnees['age_assure'] ?? null,
            'affection_grave_declaree_libelle' => $ouiNon($donnees['affection_grave_declaree'] ?? ''),
            'affection_grave_precision' => $donnees['affection_grave_precision'] ?? null,
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
            'tenant.clients.pdf.mandat-assurance-obseques',
            $data
        );

        $filename = 'Mandat-Assurance-Obseques-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

