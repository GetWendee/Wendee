<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cabinet_profiles', function (Blueprint $table) {

            $table->id();

            /*
            |--------------------------------------------------------------------------
            | IDENTITÉ DU CABINET
            |--------------------------------------------------------------------------
            */

            $table->string('nom_commercial')->nullable();
            $table->string('raison_sociale')->nullable();
            $table->string('slogan')->nullable();
            $table->string('logo')->nullable();

            /*
            |--------------------------------------------------------------------------
            | IDENTIFICATION JURIDIQUE
            |--------------------------------------------------------------------------
            */

            $table->string('forme_juridique')->nullable();
            $table->string('siren')->nullable();
            $table->string('siret')->nullable();

            /*
            |--------------------------------------------------------------------------
            | COORDONNÉES
            |--------------------------------------------------------------------------
            */

            $table->string('adresse')->nullable();
            $table->string('adresse_ligne_2')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->nullable();

            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->string('site_internet')->nullable();

            /*
            |--------------------------------------------------------------------------
            | RÉGLEMENTATION
            |--------------------------------------------------------------------------
            */

            $table->string('numero_orias')->nullable();
            $table->string('immatriculation_cci')->nullable();

            $table->json('statuts_reglementaires')->nullable();

            /*
            |--------------------------------------------------------------------------
            | DOMAINES D'INTERVENTION
            |--------------------------------------------------------------------------
            */

            $table->json('domaines_intervention')->nullable();

            /*
            |--------------------------------------------------------------------------
            | RÉMUNÉRATION
            |--------------------------------------------------------------------------
            */

            $table->string('mode_remuneration')->nullable();

            /*
            |--------------------------------------------------------------------------
            | PRESTATIONS
            |--------------------------------------------------------------------------
            */

            $table->json('prestations')->nullable();

            /*
            |--------------------------------------------------------------------------
            | PARTENAIRES
            |--------------------------------------------------------------------------
            */

            $table->json('partenaires')->nullable();

            /*
            |--------------------------------------------------------------------------
            | PRÉSENTATION
            |--------------------------------------------------------------------------
            */

            $table->text('presentation')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabinet_profiles');
    }
};
