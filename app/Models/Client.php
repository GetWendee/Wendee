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
// cloture_le, archive_le, notification_archivage_envoyee_le et
// demande_reactivation_le sont volontairement absents du Fillable : ils ne
// sont jamais positionnés via un formulaire, seulement par
// cloturer()/reactiver()/demanderReactivation() ci-dessous ou par la
// commande clients:traiter-clotures.
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
            'cloture_le' => 'datetime',
            'archive_le' => 'datetime',
            'notification_archivage_envoyee_le' => 'datetime',
            'demande_reactivation_le' => 'datetime',
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
                ? Formatage::premiereMajuscule($value)
                : Formatage::nomPropre($value),
        );
    }

    protected function nomJeuneFille(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => Formatage::nomPropre($value));
    }

    protected function raisonSociale(): Attribute
    {
        return Attribute::make(set: fn (?string $value) => Formatage::premiereMajuscule($value));
    }

    public function estMorale(): bool
    {
        return $this->type === 'morale';
    }

    /**
     * Relations de représentant qui caractérisent un majeur protégé
     * (tutelle, curatelle, mandat de protection future), par opposition
     * au représentant légal d'un mineur ("parent") ou aux représentants
     * d'une personne morale ("gerant", "president", "associe"...).
     */
    private const RELATIONS_MAJEUR_PROTEGE = ['tuteur', 'curateur', 'mandataire'];

    /**
     * Mineur au sens de l'âge réel (< 18 ans), calculé depuis la date de
     * naissance plutôt que depuis la colonne `mineur` : celle-ci n'est
     * positionnée qu'une fois, à la création de la fiche (voir
     * ClientController::storeRepresentant()), et ne se met pas à jour
     * automatiquement le jour des 18 ans du titulaire.
     */
    public function estMineur(): bool
    {
        return $this->date_naissance !== null
            && $this->date_naissance->age < 18;
    }

    /**
     * Majeur protégé : personne physique majeure représentée par un
     * tuteur, curateur ou mandataire (mandat de protection future).
     * Nécessite que representants() soit déjà chargée pour éviter une
     * requête par client dans les listes (portefeuille...).
     */
    public function estMajeurProtege(): bool
    {
        if ($this->estMorale() || $this->estMineur()) {
            return false;
        }

        return $this->representants->contains(
            fn ($r) => in_array($r->relation, self::RELATIONS_MAJEUR_PROTEGE, true)
        );
    }

    /**
     * Nombre de jours de conservation d'un dossier clôturé avant archivage
     * automatique, et nombre d'années pendant lesquelles un dossier archivé
     * reste réactivable (par le courtier, ou sur demande du conseiller).
     */
    public const JOURS_AVANT_ARCHIVAGE = 180;

    public const ANNEES_REACTIVATION_APRES_ARCHIVAGE = 5;

    /**
     * Uniquement les clients "actifs" : ni clôturés, ni archivés. C'est ce
     * périmètre qui doit être utilisé partout où la liste des clients d'un
     * cabinet/conseiller est affichée en usage courant (portefeuille,
     * dashboard...). Un dossier clôturé ou archivé n'en sort que via
     * Comptes clôturés.
     */
    public function scopeActifs($query)
    {
        return $query->whereNull('cloture_le');
    }

    /**
     * Clients clôturés mais pas encore archivés : ceux affichés dans la
     * section "Clôturés" de la page Comptes clôturés, réactivables
     * librement par le courtier ou le conseiller du dossier.
     */
    public function scopeClotures($query)
    {
        return $query->whereNotNull('cloture_le')->whereNull('archive_le');
    }

    /**
     * Clients archivés : section "Archivés" de la page Comptes clôturés.
     * Reste affiché pendant ANNEES_REACTIVATION_APRES_ARCHIVAGE, tant qu'une
     * réactivation est encore possible.
     */
    public function scopeArchives($query)
    {
        return $query->whereNotNull('archive_le');
    }

    /**
     * Clients comptant dans le quota de l'abonnement (voir Tenant::PALIERS_ABONNEMENT) :
     * actifs + clôturés, jamais les archivés.
     */
    public function scopeNonArchives($query)
    {
        return $query->whereNull('archive_le');
    }

    public function estCloture(): bool
    {
        return $this->cloture_le !== null && $this->archive_le === null;
    }

    public function estArchive(): bool
    {
        return $this->archive_le !== null;
    }

    /**
     * Date à laquelle ce dossier sera archivé si personne ne le réactive
     * avant, ou null s'il n'est pas clôturé (ou déjà archivé).
     */
    public function dateArchivagePrevue(): ?\Illuminate\Support\Carbon
    {
        if (! $this->estCloture()) {
            return null;
        }

        return $this->cloture_le->copy()->addDays(self::JOURS_AVANT_ARCHIVAGE);
    }

    public function joursAvantArchivage(): ?int
    {
        $date = $this->dateArchivagePrevue();

        if (! $date) {
            return null;
        }

        return (int) ceil(($date->timestamp - now()->timestamp) / 86400);
    }

    /**
     * Date au-delà de laquelle un dossier archivé ne peut plus être
     * réactivé du tout (même par le courtier), ou null s'il n'est pas
     * archivé.
     */
    public function dateLimiteReactivation(): ?\Illuminate\Support\Carbon
    {
        if (! $this->archive_le) {
            return null;
        }

        return $this->archive_le->copy()->addYears(self::ANNEES_REACTIVATION_APRES_ARCHIVAGE);
    }

    public function estEncoreReactivable(): bool
    {
        if (! $this->estArchive()) {
            return true;
        }

        return $this->dateLimiteReactivation()->isFuture();
    }

    public function demandeReactivationEnAttente(): bool
    {
        return $this->demande_reactivation_le !== null;
    }

    /**
     * Clôture le dossier : il quitte immédiatement le portefeuille actif.
     * Réinitialise les compteurs d'archivage au cas où ce dossier avait déjà
     * été clôturé/réactivé par le passé.
     */
    public function cloturer(): void
    {
        $this->cloture_le = now();
        $this->archive_le = null;
        $this->notification_archivage_envoyee_le = null;
        $this->demande_reactivation_le = null;
        $this->save();
    }

    /**
     * Réactive le dossier, qu'il soit simplement clôturé ou déjà archivé :
     * retour au portefeuille actif normal. Autorisation vérifiée par
     * l'appelant (courtier toujours, conseiller seulement tant que le
     * dossier n'est pas archivé — voir ComptesCloturesController).
     */
    public function reactiver(): void
    {
        $this->cloture_le = null;
        $this->archive_le = null;
        $this->notification_archivage_envoyee_le = null;
        $this->demande_reactivation_le = null;
        $this->save();
    }

    /**
     * Le conseiller ne peut pas réactiver lui-même un dossier archivé : il
     * ne fait que signaler au courtier qu'une réactivation est souhaitée.
     */
    public function demanderReactivation(): void
    {
        $this->demande_reactivation_le = now();
        $this->save();
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
            // L'apporteur voit ses clients dans son portefeuille (nom,
            // statut, montants) mais n'a pas accès au dossier patrimonial
            // complet : KYC, patrimoine, analyse, mission, conformité...
            'apporteur' => false,
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

    // KYC/profil investisseur propres à une personne morale, distincts de
    // kyc()/profilInvestisseur() qui restent le circuit personne physique
    // inchangé. patrimoineElements() reste commun aux deux (mêmes natures,
    // filtrées par type côté formulaire).
    public function kycMorale(): HasOne
    {
        return $this->hasOne(KycMorale::class);
    }

    public function profilInvestisseurMorale(): HasOne
    {
        return $this->hasOne(ProfilInvestisseurMorale::class);
    }

    public function intervenants(): HasMany
    {
        return $this->hasMany(Intervenant::class);
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

    /**
     * Statut du client vu par l'apporteur (étapes de sa recommandation).
     * Aujourd'hui, seuls "Prospect créé" et "Premier contact qualifié" sont
     * calculables : la proposition envoyée, la signature (code de
     * vérification de la recommandation) et le classement en dossier
     * clôturé dépendent de modules pas encore construits. Les libellés
     * existent déjà pour que le filtre soit prêt le jour où ces modules
     * arrivent.
     */
    public function statutApporteur(): array
    {
        $labels = [
            'prospect_cree' => 'Prospect créé',
            'premier_contact_qualifie' => 'Premier contact qualifié',
            'proposition_envoyee' => 'Proposition envoyée',
            'client_signe' => 'Client signé',
            'perdu_sans_suite' => 'Perdu / sans suite',
        ];

        $key = $this->kyc()->exists() ? 'premier_contact_qualifie' : 'prospect_cree';

        return ['key' => $key, 'label' => $labels[$key]];
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

        // Screening automatisé PPE / sanctions (OpenSanctions) : une
        // correspondance potentielle prime toujours sur le déclaratif du
        // KYC, y compris si le client a répondu "non" à la question PPE.
        if ($conformite && $conformite->screening_ppe_sanctions_statut === 'correspondance_potentielle') {
            $facteurs[] = ['cle' => 'screening_ppe_sanctions', 'label' => 'Correspondance potentielle détectée lors du screening PPE / sanctions', 'niveau' => 'eleve'];
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
