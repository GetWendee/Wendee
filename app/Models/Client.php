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

    /**
     * Natures de patrimoine (config/patrimoine.php) considérées comme des
     * structures ou produits sensibles au sens LCB-FT (montages complexes,
     * parts non cotées, structures multi-juridictions...).
     */
    public const NATURES_SENSIBLES = [
        'parts_de_holding', 'par_de_sci', 'girardin_industrielle',
        'autres_droits_sociaux', 'entreprise_individuelle',
        'fonds_de_commerce_clienteles', 'autres_valeurs_mobilieres',
        'autres_placements_divers',
    ];

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

    /**
     * Vrai si ce user a le droit de consulter/modifier cette fiche client.
     * Courtier : toujours. Conseiller : son client, ou tous si droit accordé.
     * Apporteur : son client apporté. Client : sa propre fiche.
     */
    public function estVisiblePar(User $user): bool
    {
        return match ($user->effectiveRole()) {
            'courtier' => true,
            'conseiller' => $this->conseiller_id === $user->id || $user->voitTousLesClients(),
            'apporteur' => $this->apporteur_id === $user->id,
            'client' => $this->user_id === $user->id,
            default => false,
        };
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


    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(ClientAnalysis::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function conformite(): HasOne
    {
        return $this->hasOne(ClientConformite::class);
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

    /**
     * Évalue le niveau de risque LCB-FT du client à partir des données déjà
     * collectées (KYC, patrimoine, fiscalité) et de la revue manuelle du
     * conseiller. C'est une aide à la décision, pas une automatisation :
     * le conseiller garde la main via ClientConformite::niveau_risque_override.
     */
    public function evaluerRisqueLcbFt(): array
    {
        $kyc = $this->kyc;
        $fiscalite = $this->patrimoineFiscalite;
        $conformite = $this->conformite;

        $facteurs = [];

        if ($kyc) {
            if ($kyc->est_ppe === 'oui_ppe') {
                $facteurs[] = ['cle' => 'ppe', 'label' => 'Client personne politiquement exposée', 'niveau' => 'eleve'];
            }
            if ($kyc->proche_ppe === 'oui_proche_ppe') {
                $facteurs[] = ['cle' => 'proche_ppe', 'label' => "Proche d'une personne politiquement exposée", 'niveau' => 'eleve'];
            }
            if ($kyc->residence_fiscale_identique === 'non') {
                $facteurs[] = ['cle' => 'non_resident', 'label' => 'Résidence fiscale différente de l’adresse principale', 'niveau' => 'eleve'];
            }
        }

        if ($fiscalite && $fiscalite->us_person === 'oui') {
            $facteurs[] = ['cle' => 'us_person', 'label' => 'US Person (obligation déclarative FATCA)', 'niveau' => 'eleve'];
        }

        $produitsSensibles = $this->patrimoineElements()
            ->whereIn('nature', self::NATURES_SENSIBLES)
            ->pluck('nature')
            ->unique();

        if ($produitsSensibles->isNotEmpty()) {
            $labels = $produitsSensibles->map(fn ($n) => config("patrimoine.natures.actif_non_financier.$n")
                ?? config("patrimoine.natures.actif_financier.$n")
                ?? $n);

            $facteurs[] = [
                'cle' => 'produits_sensibles',
                'label' => 'Détient : ' . $labels->implode(', '),
                'niveau' => $produitsSensibles->count() >= 2 ? 'eleve' : 'standard',
            ];
        }

        if ($this->apporteur_id) {
            $facteurs[] = ['cle' => 'apporteur', 'label' => 'Entrée en relation via un apporteur tiers', 'niveau' => 'standard'];
        }

        $completion = $this->completionStatus();
        if (! $completion['a_jour']) {
            $facteurs[] = ['cle' => 'dossier_incomplet', 'label' => 'Dossier KYC / Patrimoine / Profil incomplet ou périmé (> 1 an)', 'niveau' => 'eleve'];
        }

        if ($conformite && $conformite->vigilance_renforcee) {
            $facteurs[] = ['cle' => 'vigilance_manuelle', 'label' => 'Vigilance renforcée activée par le conseiller', 'niveau' => 'eleve'];
        }

        $niveauCalcule = 'faible';
        if (collect($facteurs)->contains(fn ($f) => $f['niveau'] === 'eleve')) {
            $niveauCalcule = 'eleve';
        } elseif (collect($facteurs)->contains(fn ($f) => $f['niveau'] === 'standard')) {
            $niveauCalcule = 'standard';
        }

        return [
            'niveau_calcule' => $niveauCalcule,
            'niveau_retenu' => $conformite?->niveau_risque_override ?: $niveauCalcule,
            'surcharge' => (bool) $conformite?->niveau_risque_override,
            'facteurs' => $facteurs,
        ];
    }

}
