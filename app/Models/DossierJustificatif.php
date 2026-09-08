<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Justificatif versionné rattaché à un dossier d'enrôlement (identité,
 * ORIAS, diplôme, RCP, garantie financière...). Chaque nouveau
 * téléversement pour un même type crée une nouvelle version, les
 * précédentes restent historisées (voir claude/enrolement-conseillers-mandataires.md).
 */
class DossierJustificatif extends Model
{
    protected $fillable = [
        'dossier_enrolement_id',
        'type',
        'fichier_path',
        'nom_original',
        'date_emission',
        'date_debut_validite',
        'date_expiration',
        'statut',
        'version',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'date_emission' => 'date',
            'date_debut_validite' => 'date',
            'date_expiration' => 'date',
            'version' => 'integer',
            'uploaded_at' => 'datetime',
        ];
    }

    public function dossier(): BelongsTo
    {
        return $this->belongsTo(DossierEnrolement::class, 'dossier_enrolement_id');
    }
}
