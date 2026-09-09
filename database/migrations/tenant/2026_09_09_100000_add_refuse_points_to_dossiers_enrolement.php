<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dossiers_enrolement', function (Blueprint $table) {
            $table->json('refuse_points')->nullable()->after('refuse_motif');
        });
    }

    public function down(): void
    {
        Schema::table('dossiers_enrolement', function (Blueprint $table) {
            $table->dropColumn('refuse_points');
        });
    }
};
