<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | DONNÉES SIRENE
            |--------------------------------------------------------------------------
            */

            $table->string('code_ape')->nullable()->after('siren');

            $table->string('libelle_ape')->nullable()->after('code_ape');

            $table->string('activite_principale')->nullable()->after('libelle_ape');

            $table->string('etat_administratif')->nullable()->after('activite_principale');

            $table->date('date_creation')->nullable()->after('etat_administratif');

            $table->string('enseigne')->nullable()->after('date_creation');

            $table->string('nom_unite_legale')->nullable()->after('enseigne');

            /*
            |--------------------------------------------------------------------------
            | SNAPSHOT SIRENE
            |--------------------------------------------------------------------------
            |
            | Conservation des données utilisées lors du préremplissage.
            | Utile pour l'audit et le diagnostic.
            |
            */

            $table->json('donnees_sirene')->nullable()->after('nom_unite_legale');
        });
    }

    public function down(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {

            $table->dropColumn([
                'code_ape',
                'libelle_ape',
                'activite_principale',
                'etat_administratif',
                'date_creation',
                'enseigne',
                'nom_unite_legale',
                'donnees_sirene',
            ]);
        });
    }
};
