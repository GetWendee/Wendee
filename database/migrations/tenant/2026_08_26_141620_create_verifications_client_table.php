<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verifications_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('module'); // kyc | patrimoine | profil_investisseur
            $table->string('code', 5)->nullable();
            $table->timestamp('code_envoye_le')->nullable();
            $table->timestamp('verifie_le')->nullable();
            $table->timestamps();

            $table->unique(['client_id', 'module']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications_client');
    }
};
