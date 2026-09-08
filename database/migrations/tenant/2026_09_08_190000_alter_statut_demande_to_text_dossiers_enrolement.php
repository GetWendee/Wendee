<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Le statut envisagé peut regrouper plusieurs choix (checkbox multiple),
 * on stocke un tableau JSON : la colonne string(255) est trop courte.
 * Modification par SQL brut pour ne pas dépendre de doctrine/dbal.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE dossiers_enrolement MODIFY statut_demande TEXT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE dossiers_enrolement ALTER COLUMN statut_demande TYPE TEXT');
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE dossiers_enrolement MODIFY statut_demande VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE dossiers_enrolement ALTER COLUMN statut_demande TYPE VARCHAR(255)');
        }
    }
};
