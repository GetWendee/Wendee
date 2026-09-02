<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'civilite', 'prenom', 'nom', 'nom_jeune_fille', 'date_naissance',
    'telephone_mobile', 'telephone_domicile', 'email',
    'adresse', 'code_postal', 'ville', 'pays', 'conseiller_id', 'apporteur_id', 'user_id',
])]
class Client extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }

    public function conseiller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conseiller_id');
    }

    public function apporteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'apporteur_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kyc(): HasOne
    {
        return $this->hasOne(ClientKyc::class);
    }

    public function profilInvestisseur(): HasOne
    {
        return $this->hasOne(ProfilInvestisseur::class);
    }

    public function patrimoineElements(): HasMany
    {
        return $this->hasMany(PatrimoineElement::class);
    }

    public function personnesACharge(): HasMany
    {
        return $this->hasMany(ClientPersonneACharge::class);
    }

    public function patrimoineFiscalite(): HasOne
    {
        return $this->hasOne(PatrimoineFiscalite::class);
    }

    public function patrimoineObjectifs(): HasMany
    {
        return $this->hasMany(PatrimoineObjectif::class);
    }


    public function analyses(): HasMany
    {
        return $this->hasMany(ClientAnalysis::class);
    }

    public function completionStatus(): array
    {
        $oneYearAgo = now()->subYear();

        $kycDone = $this->kyc()->exists();
        $kycDate = $this->kyc?->updated_at;

        $patDone = $this->patrimoineElements()->exists();
        $patDate = $this->patrimoineElements->max('updated_at');

        $invDone = $this->profilInvestisseur()->exists();
        $invDate = $this->profilInvestisseur?->updated_at;

        $items = [
            'kyc' => [
                'done' => $kycDone,
                'stale' => $kycDone && $kycDate && $kycDate->lt($oneYearAgo),
            ],
            'pat' => [
                'done' => $patDone,
                'stale' => $patDone && $patDate && $patDate->lt($oneYearAgo),
            ],
            'inv' => [
                'done' => $invDone,
                'stale' => $invDone && $invDate && $invDate->lt($oneYearAgo),
            ],
        ];

        $allDone = $kycDone && $patDone && $invDone;
        $anyStale = $items['kyc']['stale'] || $items['pat']['stale'] || $items['inv']['stale'];

        return [
            'items' => $items,
            'a_jour' => $allDone && ! $anyStale,
        ];
    }

}
