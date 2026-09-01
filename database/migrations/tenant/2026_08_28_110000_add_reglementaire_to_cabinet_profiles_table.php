<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->string('ville_rcs')->nullable()->after('numero_rcs');
            $table->string('numero_tva')->nullable()->after('statuts_reglementaires');
            $table->string('assurance_compagnie')->nullable()->after('numero_tva');
            $table->string('assurance_police')->nullable()->after('assurance_compagnie');
            $table->string('garantie_financiere')->nullable()->after('assurance_police');
            $table->string('association_professionnelle')->nullable()->after('garantie_financiere');
            $table->string('mediateur_nom')->nullable()->after('association_professionnelle');
            $table->string('mediateur_contact')->nullable()->after('mediateur_nom');
        });
    }
    public function down(): void
    {
        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'ville_rcs',
                'numero_tva',
                'assurance_compagnie',
                'assurance_police',
                'garantie_financiere',
                'association_professionnelle',
                'mediateur_nom',
                'mediateur_contact',
            ]);
        });
    }
};
