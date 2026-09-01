<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {

            $table->string('libelle_forme_juridique')
                ->nullable()
                ->after('forme_juridique');

            $table->string('libelle_ape_2025')
                ->nullable()
                ->after('code_ape_2025');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {

            $table->dropColumn([
                'libelle_forme_juridique',
                'libelle_ape_2025',
            ]);
        });
    }
};
