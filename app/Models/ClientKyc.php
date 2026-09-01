<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'ne_en_france', 'commune_naissance', 'code_postal_naissance', 'pays_naissance',
    'francais', 'autre_nationalite',
    'classification_mif', 'capacite_juridique',
    'situation_familiale', 'date_mariage', 'lieu_mariage', 'regime_matrimonial',
    'donation_dernier_vivant_profit', 'donation_dernier_vivant_conjoint',
    'date_pacs', 'lieu_pacs', 'convention_pacs', 'a_conjoint', 'a_personnes_a_charge',
    'conjoint_civilite', 'conjoint_nom', 'conjoint_nom_naissance', 'conjoint_prenom', 'conjoint_date_naissance',
    'statut_professionnel', 'societe_employeur', 'date_entree_entreprise', 'profession_libelle',
    'code_naf', 'age_depart_retraite', 'csp', 'siret_employeur',
    'conjoint_ajouter_profession', 'conjoint_statut_professionnel', 'conjoint_societe_employeur',
    'conjoint_date_entree_entreprise', 'conjoint_profession_libelle', 'conjoint_code_naf',
    'conjoint_age_depart_retraite', 'conjoint_csp', 'conjoint_siret_employeur',
    'residence_fiscale_identique', 'autre_pays_residence_fiscale', 'heberge_par_tiers',
    'est_ppe', 'ppe_exercice_12_mois', 'ppe_fonction', 'ppe_date_debut', 'ppe_date_fin', 'ppe_pays',
    'proche_ppe', 'proche_ppe_exercice_12_mois', 'proche_ppe_fonction', 'proche_ppe_nom',
    'proche_ppe_prenom', 'proche_ppe_nature_lien', 'proche_ppe_date_debut', 'proche_ppe_date_fin', 'proche_ppe_pays',
    'lieu_signature', 'accepte_cgu', 'signe_le',
])]
class ClientKyc extends Model
{
    protected $table = 'client_kyc';

    protected function casts(): array
    {
        return [
            'date_mariage' => 'date',
            'date_pacs' => 'date',
            'conjoint_date_naissance' => 'date',
            'date_entree_entreprise' => 'date',
            'conjoint_date_entree_entreprise' => 'date',
            'ppe_date_debut' => 'date',
            'ppe_date_fin' => 'date',
            'proche_ppe_date_debut' => 'date',
            'proche_ppe_date_fin' => 'date',
            'a_conjoint' => 'boolean',
            'a_personnes_a_charge' => 'boolean',
            'conjoint_ajouter_profession' => 'boolean',
            'accepte_cgu' => 'boolean',
            'signe_le' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
