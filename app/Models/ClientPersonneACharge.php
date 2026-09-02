<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['civilite', 'prenom', 'nom', 'date_naissance', 'enfant_de', 'fiscalement_a_charge', 'garde_alternee', 'invalidite'])]
class ClientPersonneACharge extends Model
{
    protected $table = 'client_personnes_a_charge';

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
