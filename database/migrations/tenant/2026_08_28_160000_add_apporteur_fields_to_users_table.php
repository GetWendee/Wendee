<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'apporteur_forme_juridique' => fn ($t) => $t->string('apporteur_forme_juridique')->nullable(),
                'apporteur_denomination_sociale' => fn ($t) => $t->string('apporteur_denomination_sociale')->nullable(),
                'apporteur_date_creation' => fn ($t) => $t->date('apporteur_date_creation')->nullable(),
                'apporteur_siren' => fn ($t) => $t->string('apporteur_siren')->nullable(),
                'apporteur_siret' => fn ($t) => $t->string('apporteur_siret')->nullable(),
                'apporteur_rcs_ville' => fn ($t) => $t->string('apporteur_rcs_ville')->nullable(),
                'apporteur_rcs_numero' => fn ($t) => $t->string('apporteur_rcs_numero')->nullable(),
                'apporteur_representant_legal' => fn ($t) => $t->string('apporteur_representant_legal')->nullable(),
                'apporteur_immatricule_orias' => fn ($t) => $t->boolean('apporteur_immatricule_orias')->nullable(),
                'apporteur_roles' => fn ($t) => $t->json('apporteur_roles')->nullable(),
                'apporteur_role_commentaire' => fn ($t) => $t->string('apporteur_role_commentaire')->nullable(),
                'apporteur_orias_numero' => fn ($t) => $t->string('apporteur_orias_numero')->nullable(),
                'apporteur_statut_reglemente' => fn ($t) => $t->json('apporteur_statut_reglemente')->nullable(),
                'apporteur_autorite_controle' => fn ($t) => $t->json('apporteur_autorite_controle')->nullable(),
                'apporteur_rcp' => fn ($t) => $t->boolean('apporteur_rcp')->nullable(),
                'apporteur_rcp_compagnie' => fn ($t) => $t->string('apporteur_rcp_compagnie')->nullable(),
            ];

            foreach ($columns as $name => $definer) {
                if (! Schema::hasColumn('users', $name)) {
                    $definer($table);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $names = [
                'apporteur_forme_juridique', 'apporteur_denomination_sociale', 'apporteur_date_creation',
                'apporteur_siren', 'apporteur_siret', 'apporteur_rcs_ville', 'apporteur_rcs_numero',
                'apporteur_representant_legal', 'apporteur_immatricule_orias', 'apporteur_roles',
                'apporteur_role_commentaire', 'apporteur_orias_numero', 'apporteur_statut_reglemente',
                'apporteur_autorite_controle', 'apporteur_rcp', 'apporteur_rcp_compagnie',
            ];
            foreach ($names as $name) {
                if (Schema::hasColumn('users', $name)) {
                    $table->dropColumn($name);
                }
            }
        });
    }
};
