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
            | ABONNEMENT — NOMBRE DE CLIENTS MAX
            |--------------------------------------------------------------------------
            |
            | Palier choisi parmi Tenant::PALIERS_ABONNEMENT. abonnement_modifie_par_nom
            | est un instantané du nom (pas une clé étrangère) : il reste lisible même
            | si le compte qui a fait le changement est supprimé plus tard.
            |
            */

            $table->unsignedInteger('abonnement_nombre_clients_max')
                ->nullable()
                ->after('code_ape');

            $table->string('abonnement_modifie_par_nom')
                ->nullable()
                ->after('abonnement_nombre_clients_max');

            $table->timestamp('abonnement_modifie_le')
                ->nullable()
                ->after('abonnement_modifie_par_nom');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'abonnement_nombre_clients_max',
                'abonnement_modifie_par_nom',
                'abonnement_modifie_le',
            ]);
        });
    }
};
