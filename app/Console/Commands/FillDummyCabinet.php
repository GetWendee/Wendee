<?php

namespace App\Console\Commands;

use App\Models\CabinetProfile;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class FillDummyCabinet extends Command
{
    protected $signature = 'cabinet:fill-dummy {tenant} {email}';

    protected $description = 'Renseigne des valeurs bidon dans le profil cabinet et les objectifs utilisateur pour débloquer la création de clients';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $email = $this->argument('email');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");
            return self::FAILURE;
        }

        $resultat = null;

        $tenant->run(function () use ($email, &$resultat) {
            $cabinet = CabinetProfile::query()->first();

            if (! $cabinet) {
                $resultat = 'cabinet_introuvable';
                return;
            }

            $user = User::where('email', $email)->first();

            if (! $user) {
                $resultat = 'user_introuvable';
                return;
            }

            $cabinet->update([
                'nom_commercial' => 'Cabinet Wendee',
                'raison_sociale' => 'WENDEE',
                'forme_juridique' => 'SAS',
                'numero_rcs' => '123 456 789',
                'ville_rcs' => 'Paris',
                'capital_social' => 10000,
                'numero_tva' => 'FR12345678901',
                'logo' => 'logos/placeholder.png',
                'adresse' => '1 rue de la Paix',
                'code_postal' => '75002',
                'ville' => 'Paris',
                'telephone' => '0600000000',
                'email' => $email,
                'numero_orias' => '12345678',
                'statuts_reglementaires' => ['cif'],
                'mode_remuneration' => 'honoraires_commissions',
                'prestations' => [
                    ['mode' => 'pourcentage', 'forfait' => null, 'pourcentage' => 1],
                    ['mode' => 'pourcentage', 'forfait' => null, 'pourcentage' => 1],
                    ['mode' => 'pourcentage', 'forfait' => null, 'pourcentage' => 1],
                ],
            ]);

            $user->update([
                'objectifs' => [
                    'client_semaine' => 5,
                    'rdv_semaine' => 10,
                    'collectes_semaine' => 3,
                    'taux_transformation' => 20,
                    'revenu_mensuel' => 5000,
                    'revenu_annuel' => 60000,
                ],
            ]);

            $resultat = 'ok';
        });

        if ($resultat === 'cabinet_introuvable') {
            $this->error("Aucun profil cabinet trouvé dans ce tenant : {$tenantId}");
            return self::FAILURE;
        }

        if ($resultat === 'user_introuvable') {
            $this->error("Utilisateur introuvable dans ce tenant : {$email}");
            return self::FAILURE;
        }

        $this->info("Valeurs bidon renseignées pour le cabinet et l'utilisateur {$email} dans le tenant {$tenantId}.");
        return self::SUCCESS;
    }
}
