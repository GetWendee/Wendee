<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_personnes_a_charge', function (Blueprint $table) {
            if (! Schema::hasColumn('client_personnes_a_charge', 'titulaire_id')) {
                // Lien vers la fiche propre du mineur représenté (table clients),
                // quand cette personne à charge est aussi le titulaire d'un
                // contrat (voir representants). Nullable : une personne à
                // charge "classique" n'a pas forcément sa propre fiche.
                $table->foreignId('titulaire_id')->nullable()->after('client_id')->constrained('clients')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_personnes_a_charge', function (Blueprint $table) {
            if (Schema::hasColumn('client_personnes_a_charge', 'titulaire_id')) {
                $table->dropConstrainedForeignId('titulaire_id');
            }
        });
    }
};
