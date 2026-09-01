<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilInvestisseur extends Model
{
    protected $table = 'profil_investisseur';

    protected $fillable = [
        'client_id',
        'reponses',
        'niveau_connaissance_simple',
        'niveau_experience_simple',
        'niveau_connaissance_intermediaire',
        'niveau_experience_intermediaire',
        'niveau_connaissance_complexe',
        'niveau_experience_complexe',
        'score_complexe_resultat',
        'score_complexe_echelle',
        'score_connaissance_global',
        'score_connaissance_global_echelle',
        'score_experience_global',
        'score_experience_global_echelle',
        'score_capacite_financiere',
        'score_capacite_financiere_echelle',
        'score_contrainte_financiere',
        'score_tolerance_risque',
        'score_tolerance_risque_echelle',
        'd1_tolerance_declarative',
        'd3_comportement_risque',
        'score_capacite_subir_pertes',
        'score_capacite_subir_pertes_echelle',
        'profil_risque_final',
        'profil_risque_final_echelle',
        'alerte_client_fragile',
        'alerte_client_fragile_echelle',
        'alerte_objectif_levier',
        'alerte_objectif_levier_echelle',
        'alerte_detention_sans_experience_marche',
        'alerte_detention_sans_experience_marches_echelle',
        'alerte_profil_instable',
        'alerte_profil_instable_echelle',
        'alerte_effort_epargne',
        'alerte_effort_epargne_echelle',
        'alerte_complexes_sans_experience',
        'alerte_complexes_sans_experience_echelle',
        'alerte_horizon_incompatible',
        'alerte_horizon_incompatible_echelle',
        'blocage_depense_imprevue',
        'blocage_produits_complexes_interdits',
        'coherence_bloc_1',
        'coherence_bloc_1_echelle',
        'engagement_extra_financier_score',
        'orientation_extra_financier_score',
        'thematiques_esg_score',
        'intensite_extra_financier_score',
        'engagement_extra_financier_echelle',
        'orientation_extra_financier_echelle',
        'thematiques_esg_echelle',
        'rintensite_extra_financier_echelle',
        'score_esg',
        'alerte_esg_echelle',
        'signe_le',
        'accepte_cgu',
    ];

    protected function casts(): array
    {
        return [
            'reponses' => 'array',
            'signe_le' => 'date',
            'accepte_cgu' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
