<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class ResetTenantUserPassword extends Command
{
    protected $signature = 'user:reset-password {tenant} {email} {password}';

    protected $description = 'Réinitialise le mot de passe d\'un utilisateur dans la base d\'un cabinet (tenant)';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $email = $this->argument('email');
        $password = $this->argument('password');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");
            return self::FAILURE;
        }

        $resultat = null;

        $tenant->run(function () use ($email, $password, &$resultat) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $resultat = 'introuvable';
                return;
            }

            $user->password = $password;
            $user->save();
            $resultat = 'ok';
        });

        if ($resultat === 'introuvable') {
            $this->error("Utilisateur introuvable dans ce tenant : {$email}");
            return self::FAILURE;
        }

        $this->info("Mot de passe mis à jour pour {$email} dans le tenant {$tenantId}.");
        return self::SUCCESS;
    }
}
