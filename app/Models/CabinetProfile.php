<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CabinetProfile extends Model
{
    protected $fillable = [
        'nom_commercial',
        'raison_sociale',
        'slogan',
        'logo',

        'forme_juridique',
        'capital_social',
        'numero_rcs',
        'ville_rcs',
        'siren',
        'siret',

        'code_ape',
        'libelle_ape',
        'activite_principale',
        'etat_administratif',
        'date_creation',
        'enseigne',
        'nom_unite_legale',
        'donnees_sirene',

        'adresse',
        'adresse_ligne_2',
        'code_postal',
        'ville',
        'pays',

        'telephone',
        'email',
        'site_internet',

        'numero_orias',
        'immatriculation_cci',
        'date_orias',
        'statuts_reglementaires',
        'numero_tva',

        'association_professionnelle',
        'numero_association',

        'assurance_compagnie',
        'assurance_adresse',
        'assurance_code_postal',
        'assurance_ville',
        'plafond_garanties_sinistre_ias',
        'plafond_garanties_annee_ias',
        'plafond_garanties_sinistre_iobsp',
        'plafond_garanties_annee_iobsp',
        'plafond_garanties_sinistre_cif',
        'plafond_garanties_annee_cif',
        'responsabilite_civile_exploitation_sinistre',
        'assurance_police',
        'assurance_date_debut',
        'assurance_date_fin',
        'assurance_zone_couverture',

        'garantie_financiere_iobsp',
        'garantie_financiere_iobsp_assureur',
        'garantie_financiere_iobsp_numero',
        'garantie_financiere_iobsp_montant',
        'garantie_financiere_iobsp_date_fin',
        'garantie_financiere_immo',
        'garantie_financiere_immo_assureur',
        'garantie_financiere_immo_numero',
        'garantie_financiere_immo_montant',
        'garantie_financiere_immo_date_fin',

        'dirigeant_nom',
        'dirigeant_prenom',
        'dirigeant_fonction',
        'responsable_conformite',
        'mail_responsable_conformite',
        'lcbft_responsable_nom',
        'mediateur_nom',
        'mediateur_contact',

        'domaines_intervention',

        'mode_remuneration',
        'conflits_interets_existe',
        'conflits_interets_description',

        'prestations',
        'objectifs',
        'partenaires',

        'presentation',
    ];

    protected function casts(): array
    {
        return [
            'date_orias' => 'date',
            'statuts_reglementaires' => 'array',
            'association_professionnelle' => 'array',
            'assurance_date_debut' => 'date',
            'assurance_date_fin' => 'date',
            'garantie_financiere_iobsp_date_fin' => 'date',
            'garantie_financiere_immo_date_fin' => 'date',
            'domaines_intervention' => 'array',
            'prestations' => 'array',
            'objectifs' => 'array',
            'partenaires' => 'array',
            'donnees_sirene' => 'array',
        ];
    }
}
