<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('clients')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Origine : suggestion (IA 1) + prestation sélectionnée
            |--------------------------------------------------------------------------
            |
            | suggestion_id référence la ligne client_analyses (type = suggestion)
            | dont est issue la prestation. prestation_id est l'identifiant stable
            | (1 à 4) attribué par SuggestionAnalysisService à chaque prestation de
            | cette suggestion (voir champ "id" injecté après validation).
            |
            | prestation_snapshot est une copie figée de l'objet prestation
            | sélectionné (Bloc A) au moment de la sélection : IA 2A doit
            | s'appuyer dessus sans jamais le réinventer, même si la suggestion
            | d'origine est régénérée ensuite.
            |
            */
            $table->foreignId('suggestion_id')
                ->constrained('client_analyses')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('prestation_id');

            $table->json('prestation_snapshot');

            $table->string('categorie', 30);
            $table->string('type_document', 60);

            /*
            |--------------------------------------------------------------------------
            | État de la construction IA (IA 2A)
            |--------------------------------------------------------------------------
            |
            | pending / processing / completed / failed
            |
            */
            $table->string('status', 30)->default('pending');

            $table->string('input_version', 50)->nullable();
            $table->string('prompt_version', 50)->nullable();
            $table->string('model', 100)->nullable();

            $table->json('input_data')->nullable();
            $table->json('result_json')->nullable();
            $table->longText('raw_response')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Version éditée par le conseiller (Étape 3)
            |--------------------------------------------------------------------------
            |
            | Reprend la même structure que result_json une fois que le
            | conseiller a relu/ajusté le contenu généré. Reste null tant
            | qu'aucun enregistrement n'a été fait : on affiche alors
            | result_json comme valeur de départ du formulaire.
            |
            */
            $table->json('edited_json')->nullable();
            $table->timestamp('valide_le')->nullable();

            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->unsignedInteger('total_tokens')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['client_id', 'suggestion_id']);
            $table->index(['client_id', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missions');
    }
};
