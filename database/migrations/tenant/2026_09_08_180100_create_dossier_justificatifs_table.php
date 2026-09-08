<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossier_justificatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_enrolement_id')->constrained('dossiers_enrolement')->cascadeOnDelete();
            $table->string('type');
            $table->string('fichier_path');
            $table->string('nom_original')->nullable();
            $table->date('date_emission')->nullable();
            $table->date('date_debut_validite')->nullable();
            $table->date('date_expiration')->nullable();
            $table->string('statut')->default('a_verifier');
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dossier_justificatifs');
    }
};
