<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrimoine_fiscalites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained()->cascadeOnDelete();

            // Résidence fiscale / IRPP
            $table->string('resident_fiscal_francais')->nullable();
            $table->decimal('irpp_montant', 12, 2)->nullable();
            $table->decimal('irpp_nombre_parts', 4, 2)->nullable();
            $table->string('connait_tmi_ir')->nullable();
            $table->string('tmi_ir')->nullable();
            $table->decimal('reductions_credits_impots', 12, 2)->nullable();
            $table->decimal('impot_net_a_payer', 12, 2)->nullable();
            $table->decimal('contributions_sociales', 12, 2)->nullable();

            // IFI
            $table->string('impose_ifi')->nullable();
            $table->decimal('base_imposable_ifi', 14, 2)->nullable();
            $table->string('connait_tmi_ifi')->nullable();
            $table->string('tmi_ifi')->nullable();
            $table->decimal('reductions_ifi', 12, 2)->nullable();
            $table->decimal('ifi_net_a_payer', 12, 2)->nullable();

            // US Person
            $table->string('us_person')->nullable();
            $table->string('us_citoyen')->nullable();
            $table->string('us_resident')->nullable();
            $table->string('us_carte_verte')->nullable();
            $table->string('us_sejour')->nullable();
            $table->string('us_entite')->nullable();
            $table->string('us_autre_raison')->nullable();
            $table->string('us_tin')->nullable();

            // Objectifs (résumé)
            $table->decimal('effort_epargne_mensuel', 12, 2)->nullable();
            $table->decimal('montant_patrimoine_total', 14, 2)->nullable();
            $table->decimal('montant_revenus_annuels', 12, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrimoine_fiscalites');
    }
};
