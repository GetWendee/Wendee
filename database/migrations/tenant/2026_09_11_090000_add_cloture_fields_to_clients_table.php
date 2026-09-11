<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Clôture : le dossier disparaît des listes actives (portefeuille,
            // dashboard) mais reste réactivable librement par le courtier ou
            // le conseiller du client, pendant 6 mois.
            if (! Schema::hasColumn('clients', 'cloture_le')) {
                $table->timestamp('cloture_le')->nullable()->after('type');
            }

            // Archivage : posé automatiquement 180 jours après cloture_le si
            // personne n'a réactivé entre-temps. Le dossier n'est alors plus
            // réactivable qu'à l'initiative du courtier (ou sur demande du
            // conseiller), et seulement pendant 5 ans après archive_le.
            if (! Schema::hasColumn('clients', 'archive_le')) {
                $table->timestamp('archive_le')->nullable()->after('cloture_le');
            }

            // Empêche d'envoyer plusieurs fois la notification "archivage
            // dans 3 jours" au fil des exécutions quotidiennes de la
            // commande clients:traiter-clotures.
            if (! Schema::hasColumn('clients', 'notification_archivage_envoyee_le')) {
                $table->timestamp('notification_archivage_envoyee_le')->nullable()->after('archive_le');
            }

            // Demande de réactivation d'un dossier archivé, faite par le
            // conseiller : en attente de traitement par le courtier.
            if (! Schema::hasColumn('clients', 'demande_reactivation_le')) {
                $table->timestamp('demande_reactivation_le')->nullable()->after('notification_archivage_envoyee_le');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'cloture_le', 'archive_le',
                'notification_archivage_envoyee_le', 'demande_reactivation_le',
            ]);
        });
    }
};
