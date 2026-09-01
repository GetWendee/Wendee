<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            ['numero_association', 'string', 'numero_tva'],
            ['assurance_adresse', 'string', 'assurance_compagnie'],
            ['assurance_code_postal', 'string:20', 'assurance_adresse'],
            ['assurance_ville', 'string', 'assurance_code_postal'],
            ['plafond_garanties_sinistre_ias', 'decimal', 'assurance_ville'],
            ['plafond_garanties_annee_ias', 'decimal', 'plafond_garanties_sinistre_ias'],
            ['plafond_garanties_sinistre_iobsp', 'decimal', 'plafond_garanties_annee_ias'],
            ['plafond_garanties_annee_iobsp', 'decimal', 'plafond_garanties_sinistre_iobsp'],
            ['plafond_garanties_sinistre_cif', 'decimal', 'plafond_garanties_annee_iobsp'],
            ['plafond_garanties_annee_cif', 'decimal', 'plafond_garanties_sinistre_cif'],
            ['responsabilite_civile_exploitation_sinistre', 'decimal', 'plafond_garanties_annee_cif'],
            ['assurance_date_debut', 'date', 'assurance_police'],
            ['assurance_date_fin', 'date', 'assurance_date_debut'],
            ['assurance_zone_couverture', 'string', 'assurance_date_fin'],
            ['garantie_financiere_iobsp', 'string', 'assurance_zone_couverture'],
            ['garantie_financiere_iobsp_assureur', 'string', 'garantie_financiere_iobsp'],
            ['garantie_financiere_iobsp_numero', 'string', 'garantie_financiere_iobsp_assureur'],
            ['garantie_financiere_iobsp_montant', 'decimal', 'garantie_financiere_iobsp_numero'],
            ['garantie_financiere_iobsp_date_fin', 'date', 'garantie_financiere_iobsp_montant'],
            ['garantie_financiere_immo', 'string', 'garantie_financiere_iobsp_date_fin'],
            ['garantie_financiere_immo_assureur', 'string', 'garantie_financiere_immo'],
            ['garantie_financiere_immo_numero', 'string', 'garantie_financiere_immo_assureur'],
            ['garantie_financiere_immo_montant', 'decimal', 'garantie_financiere_immo_numero'],
            ['garantie_financiere_immo_date_fin', 'date', 'garantie_financiere_immo_montant'],
            ['dirigeant_nom', 'string', 'garantie_financiere_immo_date_fin'],
            ['dirigeant_prenom', 'string', 'dirigeant_nom'],
            ['dirigeant_fonction', 'string', 'dirigeant_prenom'],
            ['responsable_conformite', 'string', 'dirigeant_fonction'],
            ['mail_responsable_conformite', 'string', 'responsable_conformite'],
            ['lcbft_responsable_nom', 'string', 'mail_responsable_conformite'],
            ['conflits_interets_existe', 'string', 'mode_remuneration'],
            ['conflits_interets_description', 'text', 'conflits_interets_existe'],
        ];

        foreach ($columns as [$name, $type, $after]) {
            if (Schema::hasColumn('cabinet_profiles', $name)) {
                continue;
            }

            Schema::table('cabinet_profiles', function (Blueprint $table) use ($name, $type, $after) {
                if ($type === 'decimal') {
                    $table->decimal($name, 14, 2)->nullable()->after($after);
                } elseif ($type === 'date') {
                    $table->date($name)->nullable()->after($after);
                } elseif ($type === 'text') {
                    $table->text($name)->nullable()->after($after);
                } elseif ($type === 'string:20') {
                    $table->string($name, 20)->nullable()->after($after);
                } else {
                    $table->string($name)->nullable()->after($after);
                }
            });
        }

        if (Schema::hasColumn('cabinet_profiles', 'garantie_financiere')) {
            Schema::table('cabinet_profiles', function (Blueprint $table) {
                $table->dropColumn('garantie_financiere');
            });
        }

        if (Schema::hasColumn('cabinet_profiles', 'association_professionnelle')) {
            Schema::table('cabinet_profiles', function (Blueprint $table) {
                $table->dropColumn('association_professionnelle');
            });
        }

        if (! Schema::hasColumn('cabinet_profiles', 'association_professionnelle')) {
            Schema::table('cabinet_profiles', function (Blueprint $table) {
                $table->json('association_professionnelle')->nullable()->after('numero_tva');
            });
        }
    }

    public function down(): void
    {
        $columns = [
            'numero_association',
            'assurance_adresse',
            'assurance_code_postal',
            'assurance_ville',
            'plafond_garanties_sinistre_ias',
            'plafond_garanties_annee_ias',
            'plafond_garanties_sinistre_iobsp',
            'plafond_garanties_annee_iobsp',
            'plafond_garanties_sinistre_cif',
            'plafond_garanties_annee_cif',
            'responsabilite_civile_exploitation_sinistre',
            'assurance_date_debut',
            'assurance_date_fin',
            'assurance_zone_couverture',
            'garantie_financiere_iobsp',
            'garantie_financiere_iobsp_assureur',
            'garantie_financiere_iobsp_numero',
            'garantie_financiere_iobsp_montant',
            'garantie_financiere_iobsp_date_fin',
            'garantie_financiere_immo',
            'garantie_financiere_immo_assureur',
            'garantie_financiere_immo_numero',
            'garantie_financiere_immo_montant',
            'garantie_financiere_immo_date_fin',
            'dirigeant_nom',
            'dirigeant_prenom',
            'dirigeant_fonction',
            'responsable_conformite',
            'mail_responsable_conformite',
            'lcbft_responsable_nom',
            'conflits_interets_existe',
            'conflits_interets_description',
        ];

        Schema::table('cabinet_profiles', function (Blueprint $table) use ($columns) {
            $existing = array_filter($columns, fn ($c) => Schema::hasColumn('cabinet_profiles', $c));

            if ($existing) {
                $table->dropColumn($existing);
            }
        });

        if (Schema::hasColumn('cabinet_profiles', 'association_professionnelle')) {
            Schema::table('cabinet_profiles', function (Blueprint $table) {
                $table->dropColumn('association_professionnelle');
            });
        }

        Schema::table('cabinet_profiles', function (Blueprint $table) {
            $table->string('garantie_financiere')->nullable();
            $table->string('association_professionnelle')->nullable();
        });
    }
};
