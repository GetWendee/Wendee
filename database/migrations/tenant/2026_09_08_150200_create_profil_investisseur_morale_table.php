<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_investisseur_morale', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // Echelle 1-7
            $table->integer('profil_risque')->nullable();

            // Lignes répétables : libellé, horizon en années
            $table->json('objectifs')->nullable();

            // ESG / durabilité
            $table->boolean('esg_interesse')->nullable();
            $table->boolean('esg_taxonomie')->nullable();
            $table->boolean('esg_sfdr')->nullable();
            $table->boolean('esg_pai')->nullable();
            $table->boolean('esg_accepte_performance_moindre')->nullable();
            $table->decimal('esg_objectif_pourcentage', 5, 2)->nullable();
            $table->text('esg_objectif_libre')->nullable();
            // Echelle 1-7 (Attentif / Intéressé / Impliqué)
            $table->integer('esg_profil_investissement')->nullable();
            $table->integer('esg_profil_patrimoine_global')->nullable();
            $table->decimal('esg_patrimoine_global_pourcentage', 5, 2)->nullable();
            $table->text('esg_patrimoine_global_libre')->nullable();
            $table->json('esg_indicateurs_environnementaux')->nullable();
            $table->json('esg_indicateurs_sociaux')->nullable();
            $table->text('esg_objectifs_besoins_horizon')->nullable();

            $table->date('signe_le')->nullable();
            $table->boolean('accepte_cgu')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_investisseur_morale');
    }
};
