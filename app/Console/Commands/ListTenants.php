<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class ListTenants extends Command
{
    protected $signature = 'tenant:list';

    protected $description = 'Liste les cabinets (tenants) et leurs domaines';

    public function handle(): int
    {
        $tenants = Tenant::with('domains')->get();

        foreach ($tenants as $tenant) {
            $domaines = $tenant->domains->pluck('domain')->implode(', ');
            $this->line("{$tenant->id} -> {$domaines}");
        }

        return self::SUCCESS;
    }
}
