<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apporteur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('libelle_mission');
            $table->decimal('montant_tarif', 10, 2);
            $table->decimal('montant_commission', 10, 2);

            /*
            |----------------------------------------------------------------
            | Statut
            |----------------------------------------------------------------
            |
            | a_recevoir : mission facturée, fonds pas encore confirmés reçus
            | fonds_recus : fonds confirmés reçus, virement à faire
            | verse : virement fait (information seulement, pas de vrai
            |         virement bancaire déclenché depuis Wendee)
            |
            */
            $table->string('statut', 20)->default('a_recevoir');

            $table->timestamp('fonds_recus_le')->nullable();
            $table->timestamp('verse_le')->nullable();

            $table->timestamps();

            $table->index(['apporteur_id', 'statut']);
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
