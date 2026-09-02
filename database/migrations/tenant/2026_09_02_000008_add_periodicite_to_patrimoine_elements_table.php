<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimoine_elements', function (Blueprint $table) {
            $table->string('periodicite')->nullable()->after('montant');
        });
    }

    public function down(): void
    {
        Schema::table('patrimoine_elements', function (Blueprint $table) {
            $table->dropColumn('periodicite');
        });
    }
};
