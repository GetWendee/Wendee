<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kyc_morale', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // Chiffres clés
            $table->integer('masse_salariale_min')->nullable();
            $table->integer('masse_salariale_max')->nullable();
            $table->integer('masse_salariale_moyenne')->nullable();
            $table->decimal('valeur_estimee_entreprise', 15, 2)->nullable();
            $table->text('elements_statutaires_notables')->nullable();
            $table->text('autres_remarques_notables')->nullable();

            // Classification et connaissances
            $table->string('classification_mif')->nullable();
            $table->string('connaissances_financieres')->nullable();
            $table->string('connaissances_juridiques')->nullable();

            // Pratique de détention de produits de placement
            $table->boolean('detient_produits_actuellement')->nullable();
            $table->text('detient_produits_actuellement_detail')->nullable();
            $table->boolean('a_detenu_produits_passe')->nullable();
            $table->text('a_detenu_produits_passe_detail')->nullable();
            $table->boolean('supports_opcvm')->nullable();
            $table->string('supports_opcvm_classe_actif')->nullable();
            $table->boolean('produits_couverture')->nullable();
            $table->text('autres_supports')->nullable();

            // PPE : 9 catégories statutaires x 2 rôles (dirigeant exécutif,
            // contact suivi dossier), stocké en JSON plutôt qu'en colonnes.
            $table->json('ppe_reponses')->nullable();

            // Chiffre d'affaires / charges / résultat, par poste (lignes
            // répétables : libellé, montant, activité, hors France, remarques)
            $table->json('chiffre_affaires_n1')->nullable();
            $table->json('charges_n1')->nullable();
            $table->json('resultat_n1')->nullable();
            $table->json('resultats_filiales')->nullable();
            $table->text('evolutions_previsibles')->nullable();

            // Imposition
            $table->decimal('is_annee_derniere', 15, 2)->nullable();
            $table->decimal('is_annee_moyenne', 15, 2)->nullable();
            $table->text('is_evolutions_previsibles')->nullable();
            $table->decimal('taxe_professionnelle_annee_derniere', 15, 2)->nullable();
            $table->decimal('taxe_professionnelle_annee_moyenne', 15, 2)->nullable();
            $table->text('taxe_professionnelle_evolutions_previsibles')->nullable();
            $table->decimal('impots_fonciers', 15, 2)->nullable();
            $table->text('autres_impots_acquittes')->nullable();
            $table->text('remarques')->nullable();

            $table->date('signe_le')->nullable();
            $table->boolean('accepte_cgu')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kyc_morale');
    }
};
