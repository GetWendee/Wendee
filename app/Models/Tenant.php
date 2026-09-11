<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Paliers d'abonnement disponibles : nombre de clients maximum que le
     * cabinet peut gérer (clients actifs + clôturés, hors archivés).
     */
    public const PALIERS_ABONNEMENT = [5, 25, 50, 75, 100, 150, 200, 250, 300, 500, 1000, 1500, 2000];

    protected function casts(): array
    {
        return array_merge(parent::casts(), [
            'abonnement_modifie_le' => 'datetime',
        ]);
    }
}
