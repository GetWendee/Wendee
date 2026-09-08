<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers_enrolement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Cycle de statut du dossier (voir claude/enrolement-conseillers-mandataires.md).
            $table->string('statut')->default('invited');
            $table->string('statut_demande')->nullable();

            // Étape 1 : identité.
            $table->string('mode_exercice')->nullable();
            $table->string('civilite')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('nationalite')->nullable();
            $table->string('adresse')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();
            $table->string('societe_denomination')->nullable();
            $table->string('societe_forme_juridique')->nullable();
            $table->string('societe_siren')->nullable();
            $table->string('societe_siret')->nullable();
            $table->string('societe_rcs')->nullable();
            $table->string('societe_ville_rcs')->nullable();
            $table->decimal('societe_capital_social', 14, 2)->nullable();
            $table->string('societe_adresse_siege')->nullable();
            $table->string('representant_nom')->nullable();
            $table->string('representant_prenom')->nullable();
            $table->string('representant_fonction')->nullable();

            // Étape 2 : statut et périmètre professionnel.
            $table->json('domaines')->nullable();
            $table->json('statuts_reglementaires_actuels')->nullable();

            // Étape 3 : ORIAS.
            $table->string('orias_numero')->nullable();
            $table->date('orias_date_premiere_immatriculation')->nullable();
            $table->date('orias_date_dernier_renouvellement')->nullable();
            $table->json('orias_categories')->nullable();
            $table->boolean('orias_statut_actif')->nullable();
            $table->string('orias_organisme_mandant')->nullable();
            $table->string('orias_controle')->default('a_verifier');

            // Étape 4 : capacité professionnelle et honorabilité.
            $table->json('capacite_pro_fondement')->nullable();
            $table->string('capacite_pro_diplome_intitule')->nullable();
            $table->string('capacite_pro_diplome_etablissement')->nullable();
            $table->string('capacite_pro_diplome_annee')->nullable();
            $table->string('capacite_pro_diplome_niveau')->nullable();
            $table->string('capacite_pro_experience_employeur')->nullable();
            $table->string('capacite_pro_experience_fonction')->nullable();
            $table->string('capacite_pro_experience_periode')->nullable();
            $table->string('capacite_pro_formation_organisme')->nullable();
            $table->string('capacite_pro_formation_intitule')->nullable();
            $table->integer('capacite_pro_formation_heures')->nullable();
            $table->date('capacite_pro_formation_date_obtention')->nullable();
            $table->boolean('formation_continue_realisee')->nullable();
            $table->integer('formation_continue_heures')->nullable();
            $table->string('formation_continue_annee')->nullable();
            $table->string('formation_continue_organisme')->nullable();
            $table->boolean('honorabilite_declaree')->default(false);
            $table->timestamp('honorabilite_declaree_le')->nullable();

            // Étape 5 : RCP et garantie financière.
            $table->string('rcp_assureur')->nullable();
            $table->string('rcp_numero_police')->nullable();
            $table->date('rcp_date_debut')->nullable();
            $table->date('rcp_date_expiration')->nullable();
            $table->decimal('rcp_montant_garantie', 14, 2)->nullable();
            $table->decimal('rcp_franchise', 14, 2)->nullable();
            $table->boolean('encaissement_fonds')->default(false);
            $table->string('garantie_financiere_organisme')->nullable();
            $table->string('garantie_financiere_numero')->nullable();
            $table->decimal('garantie_financiere_montant', 14, 2)->nullable();
            $table->date('garantie_financiere_date_echeance')->nullable();

            // Étape 6 : périmètre réel du mandat.
            $table->string('mandat_zone')->nullable();
            $table->string('mandat_zone_detail')->nullable();
            $table->json('mandat_clientele')->nullable();
            $table->json('mandat_missions_autorisees')->nullable();
            $table->text('mandat_missions_interdites')->nullable();
            $table->decimal('mandat_remuneration_taux', 5, 2)->nullable();
            $table->string('mandat_remuneration_frequence')->nullable();
            $table->string('mandat_duree')->nullable();
            $table->integer('mandat_preavis_resiliation_jours')->nullable();

            // Étape 7 : procédures et engagements (historique versionné).
            $table->json('procedures_acceptees')->nullable();

            // Décision : conformité réglementaire (calculée) et décision cabinet (humaine).
            $table->string('conformite_reglementaire')->default('en_attente');
            $table->json('checks_conformite')->nullable();
            $table->string('decision_cabinet')->default('en_attente');
            $table->foreignId('valide_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->text('refuse_motif')->nullable();
            $table->text('notes_back_office')->nullable();

            // Convention de mandat générée.
            $table->longText('convention_html')->nullable();
            $table->json('convention_variables')->nullable();
            $table->timestamp('convention_genere_le')->nullable();
            $table->timestamp('convention_signe_le')->nullable();
            $table->string('convention_statut')->default('non_generee');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossiers_enrolement');
    }
};
