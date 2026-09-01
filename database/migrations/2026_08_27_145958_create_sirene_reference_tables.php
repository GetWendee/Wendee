<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CATÉGORIES JURIDIQUES SIRENE
        |--------------------------------------------------------------------------
        */

        Schema::create('sirene_legal_categories', function (Blueprint $table) {

            $table->id();

            $table->string('code', 10)->unique();

            $table->unsignedTinyInteger('niveau')->nullable();

            $table->string('parent_code', 10)->nullable();

            $table->string('libelle')->nullable();

            $table->text('libelle_complet')->nullable();

            $table->timestamps();

            $table->index('parent_code');
        });

        /*
        |--------------------------------------------------------------------------
        | NAF RÉVISION 2
        |--------------------------------------------------------------------------
        */

        Schema::create('sirene_naf_rev2', function (Blueprint $table) {

            $table->id();

            $table->string('code', 20)->unique();

            $table->string('niveau', 30)->nullable();

            $table->string('libelle')->nullable();

            $table->text('libelle_complet')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | NAF 2025
        |--------------------------------------------------------------------------
        */

        Schema::create('sirene_naf_2025', function (Blueprint $table) {

            $table->id();

            $table->string('code', 20)->unique();

            $table->string('niveau', 30)->nullable();

            $table->string('libelle')->nullable();

            $table->text('libelle_complet')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sirene_naf_2025');
        Schema::dropIfExists('sirene_naf_rev2');
        Schema::dropIfExists('sirene_legal_categories');
    }
};
