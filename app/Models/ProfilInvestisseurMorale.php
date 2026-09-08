<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'profil_risque', 'objectifs',
    'esg_interesse', 'esg_taxonomie', 'esg_sfdr', 'esg_pai', 'esg_accepte_performance_moindre',
    'esg_objectif_pourcentage', 'esg_objectif_libre',
    'esg_profil_investissement', 'esg_profil_patrimoine_global',
    'esg_patrimoine_global_pourcentage', 'esg_patrimoine_global_libre',
    'esg_indicateurs_environnementaux', 'esg_indicateurs_sociaux',
    'esg_objectifs_besoins_horizon', 'signe_le', 'accepte_cgu',
])]
class ProfilInvestisseurMorale extends Model
{
    protected $table = 'profil_investisseur_morale';

    protected function casts(): array
    {
        return [
            'objectifs' => 'array',
            'esg_interesse' => 'boolean',
            'esg_taxonomie' => 'boolean',
            'esg_sfdr' => 'boolean',
            'esg_pai' => 'boolean',
            'esg_accepte_performance_moindre' => 'boolean',
            'esg_indicateurs_environnementaux' => 'array',
            'esg_indicateurs_sociaux' => 'array',
            'signe_le' => 'date',
            'accepte_cgu' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
