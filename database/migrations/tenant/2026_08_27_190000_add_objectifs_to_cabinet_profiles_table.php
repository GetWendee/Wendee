<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->json('objectifs')->nullable()->after('prestations');
        });
    }
    public function down(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->dropColumn('objectifs');
        });
    }
};
