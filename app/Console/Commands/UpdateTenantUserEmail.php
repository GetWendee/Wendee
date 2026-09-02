<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class UpdateTenantUserEmail extends Command
{
    protected $signature = 'user:update-email {tenant} {current_email} {new_email}';

    protected $description = 'Change l\'email d\'un utilisateur dans la base d\'un cabinet (tenant)';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $currentEmail = $this->argument('current_email');
        $newEmail = $this->argument('new_email');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");
            return self::FAILURE;
        }

        $resultat = null;

        $tenant->run(function () use ($currentEmail, $newEmail, &$resultat) {
            $user = User::where('email', $currentEmail)->first();

            if (! $user) {
                $resultat = 'introuvable';
                return;
            }

            try {
                $user->email = $newEmail;
                $user->save();
            } catch (QueryException $e) {
                $resultat = 'email_deja_utilise';
                return;
            }

            $resultat = 'ok';
        });

        if ($resultat === 'introuvable') {
            $this->error("Utilisateur introuvable dans ce tenant : {$currentEmail}");
            return self::FAILURE;
        }

        if ($resultat === 'email_deja_utilise') {
            $this->error("Cet email est déjà utilisé par un autre compte dans ce tenant : {$newEmail}");
            return self::FAILURE;
        }

        $this->info("Email mis à jour : {$currentEmail} -> {$newEmail} dans le tenant {$tenantId}.");
        return self::SUCCESS;
    }
}
