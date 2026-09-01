<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_kyc', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained()->cascadeOnDelete();

            // Naissance / nationalité
            $table->string('ne_en_france')->nullable();
            $table->string('commune_naissance')->nullable();
            $table->string('code_postal_naissance')->nullable();
            $table->string('pays_naissance')->nullable();
            $table->string('francais')->nullable();
            $table->string('autre_nationalite')->nullable();

            // Classification / capacité
            $table->string('classification_mif')->nullable();
            $table->string('capacite_juridique')->nullable();

            // Situation familiale
            $table->string('situation_familiale')->nullable();
            $table->date('date_mariage')->nullable();
            $table->string('lieu_mariage')->nullable();
            $table->string('regime_matrimonial')->nullable();
            $table->string('donation_dernier_vivant_profit')->nullable();
            $table->string('donation_dernier_vivant_conjoint')->nullable();
            $table->date('date_pacs')->nullable();
            $table->string('lieu_pacs')->nullable();
            $table->string('convention_pacs')->nullable();
            $table->boolean('a_conjoint')->default(false);
            $table->boolean('a_personnes_a_charge')->default(false);

            // Conjoint (identité)
            $table->string('conjoint_civilite')->nullable();
            $table->string('conjoint_nom')->nullable();
            $table->string('conjoint_nom_naissance')->nullable();
            $table->string('conjoint_prenom')->nullable();
            $table->date('conjoint_date_naissance')->nullable();

            // Professionnel (client)
            $table->string('statut_professionnel')->nullable();
            $table->string('societe_employeur')->nullable();
            $table->date('date_entree_entreprise')->nullable();
            $table->string('profession_libelle')->nullable();
            $table->string('code_naf')->nullable();
            $table->unsignedTinyInteger('age_depart_retraite')->nullable();
            $table->string('csp')->nullable();
            $table->string('siret_employeur')->nullable();

            // Professionnel (conjoint)
            $table->boolean('conjoint_ajouter_profession')->default(false);
            $table->string('conjoint_statut_professionnel')->nullable();
            $table->string('conjoint_societe_employeur')->nullable();
            $table->date('conjoint_date_entree_entreprise')->nullable();
            $table->string('conjoint_profession_libelle')->nullable();
            $table->string('conjoint_code_naf')->nullable();
            $table->unsignedTinyInteger('conjoint_age_depart_retraite')->nullable();
            $table->string('conjoint_csp')->nullable();
            $table->string('conjoint_siret_employeur')->nullable();

            // Résidence fiscale
            $table->string('residence_fiscale_identique')->nullable();
            $table->string('autre_pays_residence_fiscale')->nullable();
            $table->string('heberge_par_tiers')->nullable();

            // PPE (personnel)
            $table->string('est_ppe')->nullable();
            $table->string('ppe_exercice_12_mois')->nullable();
            $table->string('ppe_fonction')->nullable();
            $table->date('ppe_date_debut')->nullable();
            $table->date('ppe_date_fin')->nullable();
            $table->string('ppe_pays')->nullable();

            // PPE (proche)
            $table->string('proche_ppe')->nullable();
            $table->string('proche_ppe_exercice_12_mois')->nullable();
            $table->string('proche_ppe_fonction')->nullable();
            $table->string('proche_ppe_nom')->nullable();
            $table->string('proche_ppe_prenom')->nullable();
            $table->string('proche_ppe_nature_lien')->nullable();
            $table->date('proche_ppe_date_debut')->nullable();
            $table->date('proche_ppe_date_fin')->nullable();
            $table->string('proche_ppe_pays')->nullable();

            // Signature
            $table->string('lieu_signature')->nullable();
            $table->boolean('accepte_cgu')->default(false);
            $table->timestamp('signe_le')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_kyc');
    }
};
