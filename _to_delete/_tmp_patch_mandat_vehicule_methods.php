    public function mandatAssuranceVehicule(
        Client $client
    ): \Illuminate\View\View
    {
        $cabinet = \App\Models\CabinetProfile::query()->first();
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_vehicule')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        return view('tenant.clients.mandat-assurance-vehicule', [
            'client' => $client,
            'cabinet' => $cabinet,
            'mandat' => $mandat,
        ]);
    }

    public function enregistrerMandatAssuranceVehicule(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'immatriculation' => ['nullable', 'string', 'max:20'],
            'marque' => ['nullable', 'string', 'max:255'],
            'modele' => ['nullable', 'string', 'max:255'],
            'date_premiere_immatriculation' => ['nullable', 'date'],
            'genre_vehicule' => ['nullable', 'string', 'in:vp,vu,moto,autre'],
            'energie' => ['nullable', 'string', 'in:essence,diesel,electrique,hybride'],
            'valeur_vehicule' => ['nullable', 'numeric'],
            'kilometrage_actuel' => ['nullable', 'integer'],
            'usage_vehicule' => ['nullable', 'string', 'in:prive,trajet_domicile_travail,professionnel,mixte'],
            'stationnement' => ['nullable', 'string', 'in:garage_ferme,parking_prive,voie_publique'],
            'conducteurs' => ['nullable', 'array'],
            'conducteurs.*.nom' => ['nullable', 'string', 'max:255'],
            'conducteurs.*.date_naissance' => ['nullable', 'date'],
            'conducteurs.*.date_permis' => ['nullable', 'date'],
            'conducteurs.*.statut' => ['nullable', 'string', 'in:principal,secondaire'],
            'bonus_malus' => ['nullable', 'numeric'],
            'nombre_sinistres_responsables_3ans' => ['nullable', 'integer'],
            'resiliation_anterieure' => ['nullable', 'string', 'in:oui,non'],
            'resiliation_anterieure_precision' => ['nullable', 'string', 'max:255'],
            'garanties_souhaitees' => ['nullable', 'array'],
            'garanties_souhaitees.*' => ['string', 'in:tiers,tiers_etendu,tous_risques,bris_de_glace,vol_incendie,assistance_0km,protection_juridique,garantie_conducteur,vehicule_remplacement'],
            'franchise_souhaitee' => ['nullable', 'string', 'in:sans_franchise,franchise_reduite,franchise_standard,franchise_elevee'],
            'assureur_actuel' => ['nullable', 'string', 'max:255'],
            'date_echeance_actuelle' => ['nullable', 'date'],
            'resiliation_hamon' => ['nullable', 'string', 'in:oui,non'],
            'commentaire_conseiller' => ['nullable', 'string', 'max:3000'],
        ]);

        $validated['conducteurs'] = array_values(array_filter(
            $validated['conducteurs'] ?? [],
            fn ($ligne) => ! empty(array_filter($ligne))
        ));

        \App\Models\ClientAnalysis::create([
            'client_id' => $client->id,
            'type' => 'mandat_assurance_vehicule',
            'status' => 'completed',
            'input_data' => $validated,
            'result_json' => $validated,
            'completed_at' => now(),
        ]);

        return redirect()
            ->route('tenant.clients.mandat-assurance-vehicule', $client)
            ->with('status', 'Mandat enregistré.');
    }

    public function telechargerMandatAssuranceVehiculePdf(
        \Illuminate\Http\Request $request,
        Client $client
    ): \Symfony\Component\HttpFoundation\Response
    {
        $mandat = $client->analyses()
            ->where('type', 'mandat_assurance_vehicule')
            ->where('status', 'completed')
            ->latest('completed_at')
            ->first();

        if (! $mandat) {
            return redirect()
                ->route('tenant.clients.mandat-assurance-vehicule', $client)
                ->with('error', 'Aucun mandat enregistré à exporter.');
        }

        $cabinet = \App\Models\CabinetProfile::query()->first();
        $conseiller = $client->conseiller;

        $nomClient = trim(
            ($client->civilite ? $client->civilite . ' ' : '')
            . $client->prenom . ' ' . $client->nom
        );

        $donnees = $mandat->result_json ?? [];

        $genreLabels = [
            'vp' => 'Véhicule particulier (VP)',
            'vu' => 'Véhicule utilitaire (VU)',
            'moto' => 'Moto / scooter',
            'autre' => 'Autre',
        ];
        $energieLabels = [
            'essence' => 'Essence',
            'diesel' => 'Diesel',
            'electrique' => 'Électrique',
            'hybride' => 'Hybride',
        ];
        $usageLabels = [
            'prive' => 'Privé uniquement',
            'trajet_domicile_travail' => 'Privé et trajet domicile-travail',
            'professionnel' => 'Professionnel',
            'mixte' => 'Mixte privé et professionnel',
        ];
        $stationnementLabels = [
            'garage_ferme' => 'Garage fermé',
            'parking_prive' => 'Parking privé non fermé',
            'voie_publique' => 'Voie publique',
        ];
        $statutConducteurLabels = [
            'principal' => 'Conducteur principal',
            'secondaire' => 'Conducteur secondaire',
        ];
        $garantiesLabels = [
            'tiers' => 'Au tiers',
            'tiers_etendu' => 'Tiers étendu',
            'tous_risques' => 'Tous risques',
            'bris_de_glace' => 'Bris de glace',
            'vol_incendie' => 'Vol et incendie',
            'assistance_0km' => 'Assistance 0 km',
            'protection_juridique' => 'Protection juridique',
            'garantie_conducteur' => 'Garantie du conducteur',
            'vehicule_remplacement' => 'Véhicule de remplacement',
        ];
        $franchiseLabels = [
            'sans_franchise' => 'Sans franchise',
            'franchise_reduite' => 'Franchise réduite',
            'franchise_standard' => 'Franchise standard',
            'franchise_elevee' => 'Franchise élevée',
        ];
        $ouiNon = fn ($valeur) => $valeur === 'oui' ? 'Oui' : ($valeur === 'non' ? 'Non' : '-');

        $conducteurs = array_map(function ($conducteur) use ($statutConducteurLabels) {
            $conducteur['statut_libelle'] = $statutConducteurLabels[$conducteur['statut'] ?? ''] ?? '-';
            return $conducteur;
        }, $donnees['conducteurs'] ?? []);

        $recueil = [
            'immatriculation' => $donnees['immatriculation'] ?? null,
            'marque' => $donnees['marque'] ?? null,
            'modele' => $donnees['modele'] ?? null,
            'date_premiere_immatriculation' => $donnees['date_premiere_immatriculation'] ?? null,
            'genre_vehicule_libelle' => $genreLabels[$donnees['genre_vehicule'] ?? ''] ?? '-',
            'energie_libelle' => $energieLabels[$donnees['energie'] ?? ''] ?? '-',
            'valeur_vehicule' => $donnees['valeur_vehicule'] ?? null,
            'kilometrage_actuel' => $donnees['kilometrage_actuel'] ?? null,
            'usage_vehicule_libelle' => $usageLabels[$donnees['usage_vehicule'] ?? ''] ?? '-',
            'stationnement_libelle' => $stationnementLabels[$donnees['stationnement'] ?? ''] ?? '-',
            'conducteurs' => $conducteurs,
            'bonus_malus' => $donnees['bonus_malus'] ?? null,
            'nombre_sinistres_responsables_3ans' => $donnees['nombre_sinistres_responsables_3ans'] ?? null,
            'resiliation_anterieure_libelle' => $ouiNon($donnees['resiliation_anterieure'] ?? ''),
            'resiliation_anterieure_precision' => $donnees['resiliation_anterieure_precision'] ?? null,
            'garanties_souhaitees_libelle' => implode(', ', array_map(
                fn ($v) => $garantiesLabels[$v] ?? $v,
                $donnees['garanties_souhaitees'] ?? []
            )) ?: '-',
            'franchise_souhaitee_libelle' => $franchiseLabels[$donnees['franchise_souhaitee'] ?? ''] ?? '-',
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
            'tenant.clients.pdf.mandat-assurance-vehicule',
            $data
        );

        $filename = 'Mandat-Assurance-Vehicule-'
            . \Illuminate\Support\Str::slug($client->nom)
            . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

