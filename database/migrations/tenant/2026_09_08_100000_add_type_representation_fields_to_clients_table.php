<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'type')) {
                $table->string('type')->default('physique')->after('id'); // physique | morale
            }
            if (! Schema::hasColumn('clients', 'mineur')) {
                $table->boolean('mineur')->default(false)->after('type');
            }
            if (! Schema::hasColumn('clients', 'raison_sociale')) {
                $table->string('raison_sociale')->nullable()->after('mineur');
            }
            if (! Schema::hasColumn('clients', 'forme_juridique')) {
                $table->string('forme_juridique')->nullable()->after('raison_sociale');
            }
            if (! Schema::hasColumn('clients', 'numero_immatriculation')) {
                $table->string('numero_immatriculation')->nullable()->after('forme_juridique');
            }
            if (! Schema::hasColumn('clients', 'adresse_siege_social')) {
                $table->string('adresse_siege_social')->nullable()->after('numero_immatriculation');
            }
            if (! Schema::hasColumn('clients', 'adresse_direction_effective')) {
                $table->string('adresse_direction_effective')->nullable()->after('adresse_siege_social');
            }
            if (! Schema::hasColumn('clients', 'regime_fiscal')) {
                $table->string('regime_fiscal')->nullable()->after('adresse_direction_effective');
            }
            if (! Schema::hasColumn('clients', 'activite_principale')) {
                $table->string('activite_principale')->nullable()->after('regime_fiscal');
            }
            if (! Schema::hasColumn('clients', 'activite_annexes')) {
                $table->string('activite_annexes')->nullable()->after('activite_principale');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'type', 'mineur', 'raison_sociale', 'forme_juridique',
                'numero_immatriculation', 'adresse_siege_social',
                'adresse_direction_effective', 'regime_fiscal',
                'activite_principale', 'activite_annexes',
            ]);
        });
    }
};
