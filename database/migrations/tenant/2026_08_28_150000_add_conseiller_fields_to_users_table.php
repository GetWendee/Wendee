<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'perimetres')) {
                $table->json('perimetres')->nullable()->after('activation_pending');
            }
            if (! Schema::hasColumn('users', 'habilitations')) {
                $table->json('habilitations')->nullable()->after('perimetres');
            }
            if (! Schema::hasColumn('users', 'numero_orias')) {
                $table->string('numero_orias')->nullable()->after('habilitations');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['perimetres', 'habilitations', 'numero_orias'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
