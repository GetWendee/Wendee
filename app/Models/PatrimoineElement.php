<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id',
    'categorie',
    'nature',
    'designation',
    'montant',
    'mode_detention',
    'type_pret',
    'date_souscription',
    'duree',
    'taux_interet',
    'taux_assurance',
    'bien',
])]
class PatrimoineElement extends Model
{
    protected function casts(): array
    {
        return [
            'montant' => 'decimal:2',
            'date_souscription' => 'date',
            'taux_interet' => 'decimal:2',
            'taux_assurance' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
