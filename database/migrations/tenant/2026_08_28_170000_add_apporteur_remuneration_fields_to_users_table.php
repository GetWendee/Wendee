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
                'apporteur_autre_reseau' => fn ($t) => $t->boolean('apporteur_autre_reseau')->nullable(),
                'apporteur_nom_reseau' => fn ($t) => $t->string('apporteur_nom_reseau')->nullable(),
                'apporteur_mode_acquisition' => fn ($t) => $t->json('apporteur_mode_acquisition')->nullable(),
                'apporteur_typologie_client' => fn ($t) => $t->json('apporteur_typologie_client')->nullable(),
                'apporteur_volume_mensuel_reco' => fn ($t) => $t->string('apporteur_volume_mensuel_reco')->nullable(),
                'apporteur_zone_geographique' => fn ($t) => $t->string('apporteur_zone_geographique')->nullable(),
                'apporteur_type_remuneration' => fn ($t) => $t->string('apporteur_type_remuneration')->nullable(),
                'apporteur_remuneration_pourcentage' => fn ($t) => $t->decimal('apporteur_remuneration_pourcentage', 5, 2)->nullable(),
                'apporteur_remuneration_fixe' => fn ($t) => $t->decimal('apporteur_remuneration_fixe', 10, 2)->nullable(),
                'apporteur_declenchement_remuneration' => fn ($t) => $t->string('apporteur_declenchement_remuneration')->nullable(),
                'apporteur_remuneration_produit_reglemente' => fn ($t) => $t->boolean('apporteur_remuneration_produit_reglemente')->nullable(),
                'apporteur_engagement_sans_conseil' => fn ($t) => $t->boolean('apporteur_engagement_sans_conseil')->nullable(),
                'apporteur_engagement_sans_presentation' => fn ($t) => $t->boolean('apporteur_engagement_sans_presentation')->nullable(),
                'apporteur_engagement_sans_encaissement' => fn ($t) => $t->boolean('apporteur_engagement_sans_encaissement')->nullable(),
                'apporteur_engagement_orientation' => fn ($t) => $t->boolean('apporteur_engagement_orientation')->nullable(),
                'apporteur_engagement_conformite' => fn ($t) => $t->boolean('apporteur_engagement_conformite')->nullable(),
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
                'apporteur_autre_reseau', 'apporteur_nom_reseau', 'apporteur_mode_acquisition',
                'apporteur_typologie_client', 'apporteur_volume_mensuel_reco', 'apporteur_zone_geographique',
                'apporteur_type_remuneration', 'apporteur_remuneration_pourcentage', 'apporteur_remuneration_fixe',
                'apporteur_declenchement_remuneration', 'apporteur_remuneration_produit_reglemente',
                'apporteur_engagement_sans_conseil', 'apporteur_engagement_sans_presentation',
                'apporteur_engagement_sans_encaissement', 'apporteur_engagement_orientation',
                'apporteur_engagement_conformite',
            ];
            foreach ($names as $name) {
                if (Schema::hasColumn('users', $name)) {
                    $table->dropColumn($name);
                }
            }
        });
    }
};
