<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'titulaire_id', 'user_id', 'relation', 'pouvoirs',
    'justificatif_path', 'date_debut', 'date_fin',
])]
class Representant extends Model
{
    protected function casts(): array
    {
        return [
            'pouvoirs' => 'array',
            'date_debut' => 'date',
            'date_fin' => 'date',
        ];
    }

    public function titulaire(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'titulaire_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function aLePouvoir(string $pouvoir): bool
    {
        return in_array($pouvoir, $this->pouvoirs ?? [], true);
    }
}
