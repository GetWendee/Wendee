<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_investisseur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->json('reponses')->nullable();

            $table->decimal('niveau_connaissance_simple', 8, 2)->nullable();
            $table->decimal('niveau_experience_simple', 8, 2)->nullable();
            $table->decimal('niveau_connaissance_intermediaire', 8, 2)->nullable();
            $table->decimal('niveau_experience_intermediaire', 8, 2)->nullable();
            $table->decimal('niveau_connaissance_complexe', 8, 2)->nullable();
            $table->decimal('niveau_experience_complexe', 8, 2)->nullable();
            $table->decimal('score_complexe_resultat', 8, 2)->nullable();
            $table->string('score_complexe_echelle')->nullable();
            $table->decimal('score_connaissance_global', 8, 2)->nullable();
            $table->string('score_connaissance_global_echelle')->nullable();
            $table->decimal('score_experience_global', 8, 2)->nullable();
            $table->string('score_experience_global_echelle')->nullable();
            $table->decimal('score_capacite_financiere', 8, 2)->nullable();
            $table->string('score_capacite_financiere_echelle')->nullable();
            $table->decimal('score_contrainte_financiere', 8, 2)->nullable();
            $table->decimal('score_tolerance_risque', 8, 2)->nullable();
            $table->string('score_tolerance_risque_echelle')->nullable();
            $table->decimal('d1_tolerance_declarative', 8, 2)->nullable();
            $table->decimal('d3_comportement_risque', 8, 2)->nullable();
            $table->decimal('score_capacite_subir_pertes', 8, 2)->nullable();
            $table->string('score_capacite_subir_pertes_echelle')->nullable();
            $table->decimal('profil_risque_final', 8, 2)->nullable();
            $table->string('profil_risque_final_echelle')->nullable();
            $table->decimal('alerte_client_fragile', 8, 2)->nullable();
            $table->string('alerte_client_fragile_echelle')->nullable();
            $table->decimal('alerte_objectif_levier', 8, 2)->nullable();
            $table->string('alerte_objectif_levier_echelle')->nullable();
            $table->decimal('alerte_detention_sans_experience_marche', 8, 2)->nullable();
            $table->string('alerte_detention_sans_experience_marches_echelle')->nullable();
            $table->decimal('alerte_profil_instable', 8, 2)->nullable();
            $table->string('alerte_profil_instable_echelle')->nullable();
            $table->decimal('alerte_effort_epargne', 8, 2)->nullable();
            $table->string('alerte_effort_epargne_echelle')->nullable();
            $table->decimal('alerte_complexes_sans_experience', 8, 2)->nullable();
            $table->string('alerte_complexes_sans_experience_echelle')->nullable();
            $table->decimal('alerte_horizon_incompatible', 8, 2)->nullable();
            $table->string('alerte_horizon_incompatible_echelle')->nullable();
            $table->decimal('blocage_depense_imprevue', 8, 2)->nullable();
            $table->decimal('blocage_produits_complexes_interdits', 8, 2)->nullable();
            $table->decimal('coherence_bloc_1', 8, 2)->nullable();
            $table->string('coherence_bloc_1_echelle')->nullable();
            $table->decimal('engagement_extra_financier_score', 8, 2)->nullable();
            $table->decimal('orientation_extra_financier_score', 8, 2)->nullable();
            $table->decimal('thematiques_esg_score', 8, 2)->nullable();
            $table->decimal('intensite_extra_financier_score', 8, 2)->nullable();
            $table->string('engagement_extra_financier_echelle')->nullable();
            $table->string('orientation_extra_financier_echelle')->nullable();
            $table->string('thematiques_esg_echelle')->nullable();
            $table->string('rintensite_extra_financier_echelle')->nullable();
            $table->decimal('score_esg', 8, 2)->nullable();
            $table->string('alerte_esg_echelle')->nullable();

            $table->date('signe_le')->nullable();
            $table->boolean('accepte_cgu')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_investisseur');
    }
};
