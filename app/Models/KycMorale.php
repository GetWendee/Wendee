<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'client_id', 'masse_salariale_min', 'masse_salariale_max', 'masse_salariale_moyenne',
    'valeur_estimee_entreprise', 'elements_statutaires_notables', 'autres_remarques_notables',
    'classification_mif', 'connaissances_financieres', 'connaissances_juridiques',
    'detient_produits_actuellement', 'detient_produits_actuellement_detail',
    'a_detenu_produits_passe', 'a_detenu_produits_passe_detail',
    'supports_opcvm', 'supports_opcvm_classe_actif', 'produits_couverture', 'autres_supports',
    'ppe_reponses', 'chiffre_affaires_n1', 'charges_n1', 'resultat_n1', 'resultats_filiales',
    'evolutions_previsibles', 'is_annee_derniere', 'is_annee_moyenne', 'is_evolutions_previsibles',
    'taxe_professionnelle_annee_derniere', 'taxe_professionnelle_annee_moyenne',
    'taxe_professionnelle_evolutions_previsibles', 'impots_fonciers', 'autres_impots_acquittes',
    'remarques', 'signe_le', 'accepte_cgu',
])]
class KycMorale extends Model
{
    protected $table = 'kyc_morale';

    protected function casts(): array
    {
        return [
            'detient_produits_actuellement' => 'boolean',
            'a_detenu_produits_passe' => 'boolean',
            'supports_opcvm' => 'boolean',
            'produits_couverture' => 'boolean',
            'ppe_reponses' => 'array',
            'chiffre_affaires_n1' => 'array',
            'charges_n1' => 'array',
            'resultat_n1' => 'array',
            'resultats_filiales' => 'array',
            'signe_le' => 'date',
            'accepte_cgu' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
