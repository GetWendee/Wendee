<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrimoine_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->enum('categorie', ['actif_financier', 'actif_non_financier', 'passif', 'revenu', 'charge']);
            $table->string('nature');
            $table->string('designation')->nullable();
            $table->decimal('montant', 12, 2)->default(0);
            $table->string('mode_detention')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrimoine_elements');
    }
};
