<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_analyses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Type d'analyse
            |--------------------------------------------------------------------------
            |
            | kyc
            | patrimoine
            | profil_investisseur
            |
            */
            $table->string('type', 50);

            /*
            |--------------------------------------------------------------------------
            | État du traitement
            |--------------------------------------------------------------------------
            |
            | pending
            | processing
            | completed
            | failed
            |
            */
            $table->string('status', 30)->default('pending');

            /*
            |--------------------------------------------------------------------------
            | Versionnement
            |--------------------------------------------------------------------------
            */

            $table->string('input_version', 50)->nullable();
            $table->string('prompt_version', 50)->nullable();

            /*
            |--------------------------------------------------------------------------
            | OpenAI
            |--------------------------------------------------------------------------
            */

            $table->string('model', 100)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Données
            |--------------------------------------------------------------------------
            |
            | input_data :
            | snapshot des données utilisées pour produire l'analyse.
            |
            | result_json :
            | résultat structuré exploitable par Wendee.
            |
            | raw_response :
            | réponse brute pour audit / diagnostic.
            |
            */
            $table->json('input_data')->nullable();
            $table->json('result_json')->nullable();
            $table->longText('raw_response')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Usage API
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Suivi du traitement
            |--------------------------------------------------------------------------
            */

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Index
            |--------------------------------------------------------------------------
            */

            $table->index(['client_id', 'type']);
            $table->index(['client_id', 'type', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_analyses');
    }
};
