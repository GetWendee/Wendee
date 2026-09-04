<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rib_iban')->nullable()->after('apporteur_engagement_conformite');
            $table->string('rib_bic')->nullable()->after('rib_iban');
            $table->string('rib_titulaire')->nullable()->after('rib_bic');
            $table->boolean('rib_valide')->default(false)->after('rib_titulaire');
            $table->timestamp('rib_soumis_le')->nullable()->after('rib_valide');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rib_iban', 'rib_bic', 'rib_titulaire', 'rib_valide', 'rib_soumis_le']);
        });
    }
};
