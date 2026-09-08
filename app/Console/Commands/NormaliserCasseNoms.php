<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ClientPersonneACharge;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class NormaliserCasseNoms extends Command
{
    protected $signature = 'noms:normaliser-casse {tenant}';

    protected $description = 'Réapplique la casse "Nom Propre" (mutateurs Client/ClientPersonneACharge/User) sur les prénoms/noms déjà enregistrés dans un tenant';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");
            return self::FAILURE;
        }

        $compteur = 0;

        $tenant->run(function () use (&$compteur) {
            Client::query()->chunkById(200, function ($clients) use (&$compteur) {
                foreach ($clients as $client) {
                    $avant = [$client->prenom, $client->nom, $client->nom_jeune_fille];

                    $client->prenom = $client->prenom;
                    $client->nom = $client->nom;
                    $client->nom_jeune_fille = $client->nom_jeune_fille;

                    if ($client->isDirty()) {
                        $client->save();
                        $compteur++;
                    }

                    unset($avant);
                }
            });

            ClientPersonneACharge::query()->chunkById(200, function ($personnes) use (&$compteur) {
                foreach ($personnes as $personne) {
                    $personne->prenom = $personne->prenom;
                    $personne->nom = $personne->nom;

                    if ($personne->isDirty()) {
                        $personne->save();
                        $compteur++;
                    }
                }
            });

            User::query()->chunkById(200, function ($users) use (&$compteur) {
                foreach ($users as $user) {
                    $user->name = $user->name;

                    if ($user->isDirty()) {
                        $user->save();
                        $compteur++;
                    }
                }
            });
        });

        $this->info("{$compteur} fiche(s) mise(s) à jour dans le tenant {$tenantId}.");
        return self::SUCCESS;
    }
}
