<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_personnes_a_charge', function (Blueprint $table) {
            $table->string('garde_alternee')->nullable()->after('fiscalement_a_charge');
            $table->string('invalidite')->nullable()->after('garde_alternee');
        });
    }

    public function down(): void
    {
        Schema::table('client_personnes_a_charge', function (Blueprint $table) {
            $table->dropColumn(['garde_alternee', 'invalidite']);
        });
    }
};
