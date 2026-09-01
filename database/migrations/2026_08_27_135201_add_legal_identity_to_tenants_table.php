<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | IDENTITÉ JURIDIQUE DU CABINET
            |--------------------------------------------------------------------------
            |
            | Ces informations proviennent de SIRENE lors de la création
            | du cabinet et constituent l'identité juridique du tenant.
            |
            */

            $table->string('siren', 9)
                ->nullable()
                ->unique()
                ->after('id');

            $table->string('siret', 14)
                ->nullable()
                ->unique()
                ->after('siren');

            $table->string('raison_sociale')
                ->nullable()
                ->after('siret');

            $table->string('forme_juridique')
                ->nullable()
                ->after('raison_sociale');

            $table->string('code_ape', 10)
                ->nullable()
                ->after('forme_juridique');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {

            $table->dropUnique(['siren']);
            $table->dropUnique(['siret']);

            $table->dropColumn([
                'siren',
                'siret',
                'raison_sociale',
                'forme_juridique',
                'code_ape',
            ]);
        });
    }
};
