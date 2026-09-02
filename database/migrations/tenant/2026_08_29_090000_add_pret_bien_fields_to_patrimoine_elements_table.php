<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimoine_elements', function (Blueprint $table) {
            $table->string('type_pret')->nullable()->after('mode_detention');
            $table->date('date_souscription')->nullable()->after('type_pret');
            $table->unsignedSmallInteger('duree')->nullable()->after('date_souscription');
            $table->decimal('taux_interet', 5, 2)->nullable()->after('duree');
            $table->decimal('taux_assurance', 5, 2)->nullable()->after('taux_interet');
            $table->string('bien')->nullable()->after('taux_assurance');
        });
    }

    public function down(): void
    {
        Schema::table('patrimoine_elements', function (Blueprint $table) {
            $table->dropColumn(['type_pret', 'date_souscription', 'duree', 'taux_interet', 'taux_assurance', 'bien']);
        });
    }
};
