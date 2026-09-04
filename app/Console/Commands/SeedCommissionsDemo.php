<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\Commission;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class SeedCommissionsDemo extends Command
{
    protected $signature = 'commissions:seed-demo {tenant} {--nombre=20}';

    protected $description = "Insère des commissions fictives (à recevoir, virements à faire, versées) pour peupler la page /commissions/ en démo";

    private const MISSIONS = ['Mandat de courtage', 'Contrat assurance vie', 'Souscription SCPI', 'Contrat de prévoyance', 'PER individuel', 'Compte-titres'];

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $nombre = (int) $this->option('nombre');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");

            return self::FAILURE;
        }

        $resultat = null;

        $tenant->run(function () use ($nombre, &$resultat) {
            $apporteurs = User::where('role', 'apporteur')->get();
            $clients = Client::all();

            if ($apporteurs->isEmpty()) {
                $resultat = 'aucun_apporteur';

                return;
            }

            if ($clients->isEmpty()) {
                $resultat = 'aucun_client';

                return;
            }

            $creees = 0;

            for ($n = 0; $n < $nombre; $n++) {
                $apporteur = $apporteurs->random();
                $client = $clients->random();

                $montantTarif = random_int(500, 5000);
                $montantCommission = (int) ($montantTarif * (random_int(10, 25) / 100));

                $tirage = random_int(0, 100);
                $statut = $tirage < 40 ? 'a_recevoir' : ($tirage < 70 ? 'fonds_recus' : 'verse');

                $fondsRecusLe = null;
                $verseLe = null;

                if ($statut === 'fonds_recus' || $statut === 'verse') {
                    $fondsRecusLe = now()->copy()->subDays(random_int(3, 60));
                }

                if ($statut === 'verse') {
                    $verseLe = $fondsRecusLe->copy()->addDays(random_int(1, 10));
                }

                $commission = Commission::create([
                    'apporteur_id' => $apporteur->id,
                    'client_id' => $client->id,
                    'libelle_mission' => self::MISSIONS[array_rand(self::MISSIONS)],
                    'montant_tarif' => $montantTarif,
                    'montant_commission' => $montantCommission,
                    'statut' => $statut,
                    'fonds_recus_le' => $fondsRecusLe,
                    'verse_le' => $verseLe,
                ]);

                $dateCreation = $fondsRecusLe ? $fondsRecusLe->copy()->subDays(random_int(1, 15)) : now()->copy()->subDays(random_int(1, 30));
                $commission->forceFill(['created_at' => $dateCreation, 'updated_at' => $verseLe ?? $fondsRecusLe ?? $dateCreation])->save();

                $creees++;
            }

            $resultat = $creees;
        });

        if ($resultat === 'aucun_apporteur') {
            $this->error('Aucun apporteur trouvé sur ce tenant.');

            return self::FAILURE;
        }

        if ($resultat === 'aucun_client') {
            $this->error('Aucun client trouvé sur ce tenant.');

            return self::FAILURE;
        }

        $this->info("{$resultat} commissions fictives créées.");

        return self::SUCCESS;
    }
}
