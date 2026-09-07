<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_conformites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained()->cascadeOnDelete();

            // Origine des fonds (non couvert par le KYC actuel)
            $table->boolean('origine_fonds_documentee')->default(false);
            $table->text('origine_fonds_details')->nullable();

            // Niveau de risque validé par le conseiller (surcharge le calcul automatique)
            $table->string('niveau_risque_override')->nullable();

            // Vigilance renforcée
            $table->boolean('vigilance_renforcee')->default(false);
            $table->json('motifs_vigilance')->nullable();

            // Revue périodique
            $table->date('date_derniere_revue')->nullable();

            // Tracfin (accès restreint courtier/conseiller uniquement, jamais exposé au client)
            $table->string('tracfin_statut')->default('neant');
            $table->text('tracfin_justification')->nullable();

            $table->text('commentaire')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_conformites');
    }
};
