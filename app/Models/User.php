<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\SetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'email', 'password', 'role', 'parent_id', 'activation_pending', 'objectifs', 'perimetres', 'habilitations', 'numero_orias', 'apporteur_forme_juridique', 'apporteur_denomination_sociale', 'apporteur_date_creation', 'apporteur_siren', 'apporteur_siret', 'apporteur_rcs_ville', 'apporteur_rcs_numero', 'apporteur_representant_legal', 'apporteur_immatricule_orias', 'apporteur_roles', 'apporteur_role_commentaire', 'apporteur_orias_numero', 'apporteur_statut_reglemente', 'apporteur_autorite_controle', 'apporteur_rcp', 'apporteur_rcp_compagnie', 'apporteur_autre_reseau', 'apporteur_nom_reseau', 'apporteur_mode_acquisition', 'apporteur_typologie_client', 'apporteur_volume_mensuel_reco', 'apporteur_zone_geographique', 'apporteur_type_remuneration', 'apporteur_remuneration_pourcentage', 'apporteur_remuneration_fixe', 'apporteur_declenchement_remuneration', 'apporteur_remuneration_produit_reglemente', 'apporteur_engagement_sans_conseil', 'apporteur_engagement_sans_presentation', 'apporteur_engagement_sans_encaissement', 'apporteur_engagement_orientation', 'apporteur_engagement_conformite'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'objectifs' => 'array',
            'activation_pending' => 'boolean',
            'perimetres' => 'array',
            'habilitations' => 'array',
            'apporteur_date_creation' => 'date',
            'apporteur_immatricule_orias' => 'boolean',
            'apporteur_roles' => 'array',
            'apporteur_statut_reglemente' => 'array',
            'apporteur_autorite_controle' => 'array',
            'apporteur_rcp' => 'boolean',
            'apporteur_autre_reseau' => 'boolean',
            'apporteur_mode_acquisition' => 'array',
            'apporteur_typologie_client' => 'array',
            'apporteur_remuneration_pourcentage' => 'decimal:2',
            'apporteur_remuneration_fixe' => 'decimal:2',
            'apporteur_remuneration_produit_reglemente' => 'boolean',
            'apporteur_engagement_sans_conseil' => 'boolean',
            'apporteur_engagement_sans_presentation' => 'boolean',
            'apporteur_engagement_sans_encaissement' => 'boolean',
            'apporteur_engagement_orientation' => 'boolean',
            'apporteur_engagement_conformite' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'conseiller_id');
    }


    public function effectiveRole(): ?string
    {
        return session('dev_view_role') ?: $this->role;
    }

    public function apporteurs(): HasMany
    {
        return $this->hasMany(User::class, 'parent_id')
            ->where('role', 'apporteur');
    }

    /**
     * Rôles que ce user est autorisé à créer via "Créer un utilisateur".
     * La création de clients passe par ClientController, pas par ici.
     */
    public function canCreateUserRole(string $role): bool
    {
        return match ($this->effectiveRole()) {
            'courtier' => in_array($role, ['conseiller', 'apporteur'], true),
            'conseiller' => $role === 'apporteur',
            default => false,
        };
    }

    public function creatableUserRoles(): array
    {
        return array_values(array_filter(
            ['conseiller', 'apporteur'],
            fn (string $role) => $this->canCreateUserRole($role)
        ));
    }

    /**
     * Compte fraîchement créé (activation_pending) : email de bienvenue
     * plutôt que le mail générique "mot de passe oublié".
     */
    public function sendPasswordResetNotification($token): void
    {
        if ($this->activation_pending) {
            $this->notify(new SetPasswordNotification($token));

            return;
        }

        $this->notify(new ResetPassword($token));
    }
}
