<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedApporteursDemo extends Command
{
    protected $signature = 'apporteurs:seed-demo {tenant} {courtier_email} {--nombre=5}';

    protected $description = "Crée des apporteurs fictifs rattachés au courtier donné, pour peupler les pages Commissions / Mon RIB en démo";

    private const PRENOMS = ['Karim', 'Sarah', 'Louis', 'Chloé', 'Yanis', 'Marine'];

    private const NOMS = ['Benali', 'Faure', 'Marchand', 'Perrin', 'Colin', 'Dumas'];

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $email = $this->argument('courtier_email');
        $nombre = (int) $this->option('nombre');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");

            return self::FAILURE;
        }

        $resultat = null;

        $tenant->run(function () use ($email, $nombre, &$resultat) {
            $courtier = User::where('email', $email)->first();

            if (! $courtier) {
                $resultat = 'courtier_introuvable';

                return;
            }

            $creees = 0;

            for ($n = 0; $n < $nombre; $n++) {
                $prenom = self::PRENOMS[array_rand(self::PRENOMS)];
                $nom = self::NOMS[array_rand(self::NOMS)];
                $emailApporteur = 'demo.apporteur.'.strtolower($prenom).'.'.strtolower($nom).'.'.random_int(1000, 9999).'@mapsguy.com';

                $apporteur = User::create([
                    'name' => $prenom.' '.$nom,
                    'email' => $emailApporteur,
                    'password' => Str::random(40),
                    'role' => 'apporteur',
                    'parent_id' => $courtier->id,
                    'activation_pending' => false,
                ]);

                $apporteur->forceFill(['email_verified_at' => now()])->save();

                $creees++;
            }

            $resultat = $creees;
        });

        if ($resultat === 'courtier_introuvable') {
            $this->error("Utilisateur introuvable : {$email}");

            return self::FAILURE;
        }

        $this->info("{$resultat} apporteurs fictifs créés.");

        return self::SUCCESS;
    }
}
