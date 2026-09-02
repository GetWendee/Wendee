<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patrimoine_fiscalites', function (Blueprint $table) {
            $table->string('lieu_signature')->nullable();
            $table->boolean('accepte_cgu')->nullable()->default(false);
            $table->timestamp('signe_le')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('patrimoine_fiscalites', function (Blueprint $table) {
            $table->dropColumn(['lieu_signature', 'accepte_cgu', 'signe_le']);
        });
    }
};
