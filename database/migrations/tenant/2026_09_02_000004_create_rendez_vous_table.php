<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // conseiller
            $table->string('titre')->default('Rendez-vous client');
            $table->text('notes')->nullable();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('statut')->default('confirme'); // confirme | annule
            $table->string('calendar_provider')->nullable(); // google | microsoft | null (aucune synchro)
            $table->string('external_event_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
