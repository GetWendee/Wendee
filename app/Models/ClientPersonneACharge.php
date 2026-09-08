<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['civilite', 'prenom', 'nom', 'date_naissance', 'enfant_de', 'fiscalement_a_charge', 'garde_alternee', 'invalidite', 'titulaire_id'])]
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

    /**
     * Fiche propre de cette personne à charge, quand elle est aussi le
     * titulaire d'un contrat (mineur représenté par le client courant).
     * Nullable : une personne à charge classique n'a pas de fiche propre.
     */
    public function titulaire(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'titulaire_id');
    }
}
