<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'apporteur_id', 'client_id', 'libelle_mission',
    'montant_tarif', 'montant_commission', 'statut',
    'fonds_recus_le', 'verse_le',
])]
class Commission extends Model
{
    protected function casts(): array
    {
        return [
            'montant_tarif' => 'decimal:2',
            'montant_commission' => 'decimal:2',
            'fonds_recus_le' => 'datetime',
            'verse_le' => 'datetime',
        ];
    }

    public function apporteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apporteur_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
