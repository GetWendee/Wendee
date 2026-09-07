<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id',
    'origine_fonds_documentee', 'origine_fonds_details',
    'niveau_risque_override',
    'vigilance_renforcee', 'motifs_vigilance',
    'date_derniere_revue',
    'tracfin_statut', 'tracfin_justification',
    'commentaire',
])]
class ClientConformite extends Model
{
    protected function casts(): array
    {
        return [
            'origine_fonds_documentee' => 'boolean',
            'vigilance_renforcee' => 'boolean',
            'motifs_vigilance' => 'array',
            'date_derniere_revue' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
