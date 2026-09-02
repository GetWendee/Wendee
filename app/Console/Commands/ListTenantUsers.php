<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class ListTenantUsers extends Command
{
    protected $signature = 'tenant:users {tenant}';

    protected $description = 'Liste les utilisateurs d\'un cabinet (tenant)';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            $this->error("Tenant introuvable : {$tenantId}");
            return self::FAILURE;
        }

        $tenant->run(function () {
            $users = User::all(['id', 'email', 'role']);

            foreach ($users as $user) {
                $this->line("{$user->id} -> {$user->email} ({$user->role})");
            }
        });

        return self::SUCCESS;
    }
}
