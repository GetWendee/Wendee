<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatrimoineFiscalite extends Model
{
    protected $table = 'patrimoine_fiscalites';

    protected $fillable = [
        'client_id',
        'resident_fiscal_francais',
        'irpp_montant',
        'irpp_nombre_parts',
        'connait_tmi_ir',
        'tmi_ir',
        'reductions_credits_impots',
        'impot_net_a_payer',
        'contributions_sociales',
        'impose_ifi',
        'base_imposable_ifi',
        'connait_tmi_ifi',
        'tmi_ifi',
        'reductions_ifi',
        'ifi_net_a_payer',
        'us_person',
        'us_citoyen',
        'us_resident',
        'us_carte_verte',
        'us_sejour',
        'us_entite',
        'us_autre_raison',
        'us_tin',
        'effort_epargne_mensuel',
        'montant_patrimoine_total',
        'montant_revenus_annuels',
        'lieu_signature',
        'accepte_cgu',
        'signe_le',
    ];

    protected function casts(): array
    {
        return [
            'irpp_montant' => 'decimal:2',
            'irpp_nombre_parts' => 'decimal:2',
            'reductions_credits_impots' => 'decimal:2',
            'impot_net_a_payer' => 'decimal:2',
            'contributions_sociales' => 'decimal:2',
            'base_imposable_ifi' => 'decimal:2',
            'reductions_ifi' => 'decimal:2',
            'ifi_net_a_payer' => 'decimal:2',
            'effort_epargne_mensuel' => 'decimal:2',
            'montant_patrimoine_total' => 'decimal:2',
            'montant_revenus_annuels' => 'decimal:2',
            'accepte_cgu' => 'boolean',
            'signe_le' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
