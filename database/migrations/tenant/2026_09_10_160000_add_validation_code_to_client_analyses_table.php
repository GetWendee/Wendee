<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_analyses', function (Blueprint $table) {
            $table->string('validation_code', 10)->nullable()->after('error_message');
            $table->timestamp('validation_code_envoye_le')->nullable()->after('validation_code');
            $table->timestamp('valide_le')->nullable()->after('validation_code_envoye_le');
        });
    }

    public function down(): void
    {
        Schema::table('client_analyses', function (Blueprint $table) {
            $table->dropColumn(['validation_code', 'validation_code_envoye_le', 'valide_le']);
        });
    }
};
