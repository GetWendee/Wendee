<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Dossier d'enrôlement d'un conseiller mandataire indépendant : identité,
 * statuts réglementaires, ORIAS, capacité professionnelle, RCP/garantie
 * financière, périmètre du mandat, procédures acceptées, décision et
 * convention générée. Voir claude/enrolement-conseillers-mandataires.md.
 */
class DossierEnrolement extends Model
{
    protected $table = 'dossiers_enrolement';

    protected $fillable = [
        'user_id',
        'statut',
        'statut_demande',
        'mode_exercice',
        'civilite',
        'date_naissance',
        'nationalite',
        'adresse',
        'code_postal',
        'ville',
        'pays',
        'societe_denomination',
        'societe_forme_juridique',
        'societe_siren',
        'societe_siret',
        'societe_rcs',
        'societe_ville_rcs',
        'societe_capital_social',
        'societe_adresse_siege',
        'representant_nom',
        'representant_prenom',
        'representant_fonction',
        'domaines',
        'statuts_reglementaires_actuels',
        'orias_numero',
        'orias_date_premiere_immatriculation',
        'orias_date_dernier_renouvellement',
        'orias_categories',
        'orias_statut_actif',
        'orias_organisme_mandant',
        'orias_controle',
        'capacite_pro_fondement',
        'capacite_pro_diplome_intitule',
        'capacite_pro_diplome_etablissement',
        'capacite_pro_diplome_annee',
        'capacite_pro_diplome_niveau',
        'capacite_pro_experience_employeur',
        'capacite_pro_experience_fonction',
        'capacite_pro_experience_periode',
        'capacite_pro_formation_organisme',
        'capacite_pro_formation_intitule',
        'capacite_pro_formation_heures',
        'capacite_pro_formation_date_obtention',
        'formation_continue_realisee',
        'formation_continue_heures',
        'formation_continue_annee',
        'formation_continue_organisme',
        'honorabilite_declaree',
        'honorabilite_declaree_le',
        'rcp_assureur',
        'rcp_numero_police',
        'rcp_date_debut',
        'rcp_date_expiration',
        'rcp_montant_garantie',
        'rcp_franchise',
        'encaissement_fonds',
        'garantie_financiere_organisme',
        'garantie_financiere_numero',
        'garantie_financiere_montant',
        'garantie_financiere_date_echeance',
        'mandat_zone',
        'mandat_zone_detail',
        'mandat_clientele',
        'mandat_missions_autorisees',
        'mandat_missions_interdites',
        'mandat_remuneration_taux',
        'mandat_remuneration_frequence',
        'mandat_duree',
        'mandat_preavis_resiliation_jours',
        'procedures_acceptees',
        'conformite_reglementaire',
        'checks_conformite',
        'decision_cabinet',
        'valide_par_id',
        'valide_le',
        'refuse_motif',
        'refuse_points',
        'notes_back_office',
        'convention_html',
        'convention_variables',
        'convention_genere_le',
        'convention_signe_le',
        'convention_statut',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'statut_demande' => 'array',
            'societe_capital_social' => 'decimal:2',
            'domaines' => 'array',
            'statuts_reglementaires_actuels' => 'array',
            'orias_date_premiere_immatriculation' => 'date',
            'orias_date_dernier_renouvellement' => 'date',
            'orias_categories' => 'array',
            'orias_statut_actif' => 'boolean',
            'capacite_pro_fondement' => 'array',
            'capacite_pro_formation_heures' => 'integer',
            'capacite_pro_formation_date_obtention' => 'date',
            'formation_continue_realisee' => 'boolean',
            'formation_continue_heures' => 'integer',
            'honorabilite_declaree' => 'boolean',
            'honorabilite_declaree_le' => 'datetime',
            'rcp_date_debut' => 'date',
            'rcp_date_expiration' => 'date',
            'rcp_montant_garantie' => 'decimal:2',
            'rcp_franchise' => 'decimal:2',
            'encaissement_fonds' => 'boolean',
            'garantie_financiere_montant' => 'decimal:2',
            'garantie_financiere_date_echeance' => 'date',
            'mandat_clientele' => 'array',
            'mandat_missions_autorisees' => 'array',
            'mandat_remuneration_taux' => 'decimal:2',
            'procedures_acceptees' => 'array',
            'checks_conformite' => 'array',
            'refuse_points' => 'array',
            'valide_le' => 'datetime',
            'convention_variables' => 'array',
            'convention_genere_le' => 'datetime',
            'convention_signe_le' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function justificatifs(): HasMany
    {
        return $this->hasMany(DossierJustificatif::class);
    }

    public function valideParUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par_id');
    }
}
