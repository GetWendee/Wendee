<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('representants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('titulaire_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('relation'); // parent, tuteur, curateur, mandataire, gerant, president, associe, soi_meme...
            $table->json('pouvoirs')->nullable(); // ['signature', 'souscription', 'kyc', 'profil_investisseur', 'consultation']
            $table->string('justificatif_path')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('representants');
    }
};
