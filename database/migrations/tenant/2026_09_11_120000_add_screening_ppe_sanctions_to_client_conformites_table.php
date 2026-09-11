<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_conformites', function (Blueprint $table) {
            // Vérification automatisée PPE / sanctions internationales (API
            // OpenSanctions), en complément du déclaratif KYC (ClientKyc::est_ppe)
            // qui n'était jusqu'ici jamais confronté à une source officielle.
            $table->string('screening_ppe_sanctions_statut')->nullable();
            $table->json('screening_ppe_sanctions_resultats')->nullable();
            $table->timestamp('screening_ppe_sanctions_le')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('client_conformites', function (Blueprint $table) {
            $table->dropColumn([
                'screening_ppe_sanctions_statut',
                'screening_ppe_sanctions_resultats',
                'screening_ppe_sanctions_le',
            ]);
        });
    }
};
