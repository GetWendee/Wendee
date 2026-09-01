<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->boolean('configuration_complete')
                ->default(false)
                ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->dropColumn('configuration_complete');
        });
    }
};
