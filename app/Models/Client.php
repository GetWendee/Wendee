<?php

namespace App\Models;

use App\Support\Formatage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'civilite', 'prenom', 'nom', 'nom_jeune_fille', 'date_naissance',
    'telephone_mobile', 'telephone_domicile', 'email',
    'adresse', 'code_postal', 'ville', 'pays', 'conseiller_id', 'apporteur_id', 'user_id',
    'type', 'mineur', 'raison_sociale', 'forme_juridique', 'numero_immatriculation',
    'adresse_siege_social', 'adresse_direction_effective', 'regime_fiscal',
    'activite_principale', 'activite_annexes',
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
            'mineur' => 'boolean',
        ];
    }

    // Prénom/nom toujours enregistrés avec leur majuscule initiale, quelle
    // que soit la façon dont ils sont saisis. Les particules (de, du, le...)
    // restent en minuscule sauf en tout début de nom. Ne concerne pas
    // raison_sociale, une dénomination sociale a sa propre casse.
    protected function prenom(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => Formatage::nomPropre($value));
    }

    // Une société garde le nom exactement tel que saisi (raison sociale
    // officielle) : la casse "Nom Propre" ne s'applique qu'aux personnes.
    protected function nom(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value, array $attributes) => ($attributes['type'] ?? 'physique') === 'morale'
                ? $value
                : Formatage::nomPropre($value),
        );
    }

    protected function nomJeuneFille(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => Formatage::nomPropre($value));
    }

    public function estMorale(): bool
    {
        return $this->type === 'morale';
    }

    /**
     * Nom d'affichage du titulaire, quel que soit son type (personne
     * physique ou morale).
     */
    public function nomAffichage(): string
    {
        return $this->estMorale()
            ? (string) $this->raison_sociale
            : trim($this->prenom.' '.$this->nom);
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
     * Personnes ayant un pouvoir d'agir pour ce titulaire (représentant
     * légal d'un mineur, gérant d'une société...). Ne pas confondre avec
     * user() : un titulaire mineur ou une personne morale n'a pas de
     * compte de connexion propre, seuls ses représentants en ont un.
     */
    public function representants(): HasMany
    {
        return $this->hasMany(Representant::class, 'titulaire_id');
    }

    /**
     * Vrai si le KYC de ce titulaire est celui de son représentant (mineur
     * représenté) plutôt que le sien propre. Une personne morale a son
     * propre KYC dédié (à construire séparément), ce n'est pas ce cas.
     */
    public function kycDelegueAuRepresentant(): bool
    {
        // Un mineur n'a pas de KYC propre, c'est celui du foyer de son
        // représentant. Un majeur protégé (tutelle, curatelle, mandat de
        // protection future) garde sa propre fiche : son KYC lui
        // appartient, seul le répondant change.
        return $this->mineur;
    }

    /**
     * Le titulaire dont le KYC doit effectivement être rempli/consulté :
     * lui-même, sauf mineur représenté, auquel cas c'est la fiche de son
     * représentant (son propre foyer, où le mineur figure déjà comme
     * personne à charge).
     */
    public function titulaireKyc(): self
    {
        if (! $this->kycDelegueAuRepresentant()) {
            return $this;
        }

        $representant = $this->representants->first();

        return static::where('user_id', $representant->user_id)->first() ?? $this;
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
            // Un utilisateur 'client' voit sa propre fiche, ou celle de tout
            // titulaire qu'il représente (mineur, majeur protégé, société).
            'client' => $this->user_id === $user->id
                || $this->representants()->where('user_id', $user->id)->exists(),
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

    /**
     * Un représentant pur (tuteur, curateur, mandataire, dirigeant de
     * société) n'est pas un client à proprement parler : il n'a pas à
     * remplir de KYC, patrimoine ou profil investisseur pour lui-même,
     * ces éléments concernent le titulaire qu'il représente. Rien
     * n'empêche par ailleurs qu'il devienne client de son côté un jour.
     *
     * Exception : le parent/représentant légal d'un mineur, dont la fiche
     * porte justement le KYC délégué de son enfant (voir
     * kycDelegueAuRepresentant()) : sa propre fiche est bien un dossier
     * à suivre.
     */
    public function estRepresentantPur(): bool
    {
        if (! $this->user_id) {
            return false;
        }

        $representations = $this->user?->representations;

        if (! $representations || $representations->isEmpty()) {
            return false;
        }

        return ! $representations->contains(fn ($r) => $r->relation === 'parent');
    }

    public function completionStatus(): array
    {
        if ($this->estRepresentantPur()) {
            return [
                'items' => [
                    'kyc' => ['done' => true, 'stale' => false],
                    'pat' => ['done' => true, 'stale' => false],
                    'inv' => ['done' => true, 'stale' => false],
                ],
                'a_jour' => true,
            ];
        }

        $oneYearAgo = now()->subYear();

        $kycTitulaire = $this->titulaireKyc();
        $kycDone = $kycTitulaire->kyc()->exists();
        $kycDate = $kycTitulaire->kyc?->updated_at;

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
        $kyc = $this->titulaireKyc()->kyc;
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
