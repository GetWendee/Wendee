<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'activation_pending')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('activation_pending')->default(false)->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'activation_pending')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('activation_pending');
            });
        }
    }
};
