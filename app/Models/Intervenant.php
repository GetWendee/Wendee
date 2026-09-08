<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['client_id', 'type_intervenant', 'nom', 'role', 'pourcentage_detention'])]
class Intervenant extends Model
{
    protected function casts(): array
    {
        return [
            'pourcentage_detention' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
