<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intervenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();

            // dirigeant, actionnaire, beneficiaire_effectif
            $table->string('type_intervenant');
            $table->string('nom')->nullable();
            $table->string('role')->nullable();
            $table->decimal('pourcentage_detention', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intervenants');
    }
};
