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

            /*
            |--------------------------------------------------------------------------
            | NOMENCLATURE APE
            |--------------------------------------------------------------------------
            */

            $table->string('nomenclature_ape', 30)
                ->nullable()
                ->after('code_ape');

            $table->string('code_ape_2025', 20)
                ->nullable()
                ->after('nomenclature_ape');

            $table->string('nomenclature_ape_2025', 30)
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
                'nomenclature_ape',
                'code_ape_2025',
                'nomenclature_ape_2025',
            ]);
        });
    }
};
