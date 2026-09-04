<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientAnalysis;
use App\Models\PatrimoineElement;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class SeedPerformancesDemo extends Command
{
    protected $signature = 'performances:seed-demo {tenant} {conseiller_email} {--mois=14}';

    protected $description = "Insère des clients fictifs répartis sur plusieurs mois (patrimoine, KYC, profil investisseur, audits) pour peupler la page /performances/ en démo";

    private const PRENOMS = ['Camille', 'Julien', 'Sophie', 'Nicolas', 'Claire', 'Antoine', 'Laura', 'Maxime', 'Émilie', 'Thomas', 'Charlotte', 'Hugo', 'Manon', 'Lucas', 'Inès', 'Baptiste'];

    private const NOMS = ['Bernard', 'Petit', 'Robert', 'Richard', 'Durand', 'Leroy', 'Moreau', 'Simon', 'Laurent', 'Michel', 'Garcia', 'David', 'Roux', 'Vincent', 'Fournier', 'Girard'];

    private const NATURES_FINANCIERES = ['contrat_dassurancevie_multisupports', 'pea', 'compte_titres', 'per', 'livret_a', 'compte_a_terme'];

    private const NATURES_NON_FINANCIERES = ['residence_principale', 'scpi', 'immobilier_locatif_locatif', 'parts_de_scpi'];

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $email = $this->argument('conseiller_email');
        $mois = (int) $this->option('mois');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");

            return self::FAILURE;
        }

        $resultat = null;

        $tenant->run(function () use ($email, $mois, &$resultat) {
            $conseiller = User::where('email', $email)->first();

            if (! $conseiller) {
                $resultat = 'conseiller_introuvable';

                return;
            }

            $creees = 0;

            for ($i = $mois; $i >= 0; $i--) {
                $parMois = random_int(1, 2);

                for ($n = 0; $n < $parMois; $n++) {
                    $date = now()->copy()
                        ->subMonths($i)
                        ->setDay(random_int(1, 27))
                        ->setTime(random_int(8, 18), random_int(0, 59));

                    $prenom = self::PRENOMS[array_rand(self::PRENOMS)];
                    $nom = self::NOMS[array_rand(self::NOMS)];
                    $emailClient = 'demo.'.strtolower($prenom).'.'.strtolower($nom).'.'.random_int(1000, 9999).'@mapsguy.com';

                    $client = Client::create([
                        'civilite' => random_int(0, 1) ? 'monsieur' : 'madame',
                        'prenom' => $prenom,
                        'nom' => $nom,
                        'email' => $emailClient,
                        'telephone_mobile' => '06'.random_int(10000000, 99999999),
                        'adresse' => random_int(1, 99).' rue de la République',
                        'code_postal' => '750'.str_pad((string) random_int(1, 20), 2, '0', STR_PAD_LEFT),
                        'ville' => 'Paris',
                        'pays' => 'France',
                        'conseiller_id' => $conseiller->id,
                    ]);

                    $client->forceFill(['created_at' => $date, 'updated_at' => $date])->save();

                    $actifFinancier = random_int(30, 250) * 1000;
                    $actifNonFinancier = random_int(50, 400) * 1000;
                    $passif = (int) (($actifFinancier + $actifNonFinancier) * (random_int(10, 40) / 100));

                    PatrimoineElement::create([
                        'client_id' => $client->id,
                        'categorie' => 'actif_financier',
                        'nature' => self::NATURES_FINANCIERES[array_rand(self::NATURES_FINANCIERES)],
                        'designation' => 'Placement financier',
                        'montant' => $actifFinancier,
                    ]);

                    PatrimoineElement::create([
                        'client_id' => $client->id,
                        'categorie' => 'actif_non_financier',
                        'nature' => self::NATURES_NON_FINANCIERES[array_rand(self::NATURES_NON_FINANCIERES)],
                        'designation' => 'Bien immobilier',
                        'montant' => $actifNonFinancier,
                    ]);

                    if ($passif > 0) {
                        PatrimoineElement::create([
                            'client_id' => $client->id,
                            'categorie' => 'passif',
                            'nature' => 'emprunt_sur_residence_principale',
                            'designation' => 'Emprunt',
                            'montant' => $passif,
                        ]);
                    }

                    PatrimoineElement::query()
                        ->where('client_id', $client->id)
                        ->update(['created_at' => $date, 'updated_at' => $date]);

                    /*
                     * KYC et profil investisseur signés à la date de création
                     * du client : les clients de plus d'un an ressortent donc
                     * naturellement comme "à renouveler" dans les alertes
                     * conformité, sans logique supplémentaire.
                     */
                    if (random_int(0, 100) < 80) {
                        $kyc = $client->kyc()->create(['signe_le' => $date]);
                        $kyc->forceFill(['created_at' => $date, 'updated_at' => $date])->save();
                    }

                    if (random_int(0, 100) < 70) {
                        $profil = $client->profilInvestisseur()->create(['signe_le' => $date]);
                        $profil->forceFill(['created_at' => $date, 'updated_at' => $date])->save();
                    }

                    if (random_int(0, 100) < 70) {
                        $analyse = ClientAnalysis::create([
                            'client_id' => $client->id,
                            'type' => 'recommandation',
                            'status' => 'completed',
                            'completed_at' => $date,
                        ]);
                        $analyse->forceFill(['created_at' => $date, 'updated_at' => $date])->save();
                    }

                    $creees++;
                }
            }

            $resultat = $creees;
        });

        if ($resultat === 'conseiller_introuvable') {
            $this->error("Utilisateur introuvable : {$email}");

            return self::FAILURE;
        }

        $this->info("{$resultat} clients fictifs créés sur {$mois} mois.");

        return self::SUCCESS;
    }
}
