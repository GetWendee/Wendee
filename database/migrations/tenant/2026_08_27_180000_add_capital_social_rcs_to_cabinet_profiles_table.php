<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->decimal('capital_social', 15, 2)
                ->nullable()
                ->after('forme_juridique');
            $table->string('numero_rcs')
                ->nullable()
                ->after('capital_social');
        });
    }
    public function down(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->dropColumn(['capital_social', 'numero_rcs']);
        });
    }
};
