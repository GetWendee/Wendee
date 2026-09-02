<?php

namespace App\Http\Controllers;

use App\Models\CabinetProfile;
use App\Models\User;
use App\Services\CabinetCompletionChecker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserAccountController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $cabinet = CabinetProfile::query()->first();

        if (! $cabinet || ! CabinetCompletionChecker::isComplete($cabinet, $request->user())) {
            return redirect()->route('tenant.cabinet')
                ->with('cabinet_gate_redirect', true)
                ->with('status', "Complétez d'abord les informations essentielles de votre cabinet avant de créer un compte.");
        }

        $roles = $request->user()->creatableUserRoles();

        abort_if(empty($roles), 403);

        return view('tenant.users.create', [
            'roles' => $roles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $creator = $request->user();

        $cabinet = CabinetProfile::query()->first();

        if (! $cabinet || ! CabinetCompletionChecker::isComplete($cabinet, $creator)) {
            return redirect()->route('tenant.cabinet')
                ->with('cabinet_gate_redirect', true)
                ->with('status', "Complétez d'abord les informations essentielles de votre cabinet avant de créer un compte.");
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'in:conseiller,apporteur'],
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:10', 'regex:/^[0-9]{10}$/'],
            'perimetres' => ['nullable', 'array'],
            'perimetres.*' => ['string', 'in:Assurance,Banque,Finance,Immobilier'],
            'habilitations' => ['nullable', 'array'],
            'habilitations.*' => ['string', 'max:255'],
            'numero_orias' => ['nullable', 'string', 'max:50'],
            'apporteur_forme_juridique' => ['nullable', 'string', 'in:ei,eurl,sasu,sas,sarl,sa,snc,scp'],
            'apporteur_denomination_sociale' => ['nullable', 'string', 'max:255'],
            'apporteur_date_creation' => ['nullable', 'date'],
            'apporteur_siren' => ['nullable', 'string', 'max:20'],
            'apporteur_siret' => ['nullable', 'string', 'max:20'],
            'apporteur_rcs_ville' => ['nullable', 'string', 'max:255'],
            'apporteur_rcs_numero' => ['nullable', 'string', 'max:50'],
            'apporteur_representant_legal' => ['nullable', 'string', 'max:255'],
            'apporteur_immatricule_orias' => ['nullable', 'boolean'],
            'apporteur_roles' => ['nullable', 'array'],
            'apporteur_roles.*' => ['string', 'in:mise_relation,presentation,analyse,conseil'],
            'apporteur_role_commentaire' => ['nullable', 'string', 'max:1000'],
            'apporteur_orias_numero' => ['nullable', 'string', 'max:50'],
            'apporteur_statut_reglemente' => ['nullable', 'array'],
            'apporteur_statut_reglemente.*' => ['string', 'in:iobsp,ias,cif,mia'],
            'apporteur_autorite_controle' => ['nullable', 'array'],
            'apporteur_autorite_controle.*' => ['string', 'in:acpr,amf,autre'],
            'apporteur_rcp' => ['nullable', 'boolean'],
            'apporteur_rcp_compagnie' => ['nullable', 'string', 'max:255'],
            'apporteur_autre_reseau' => ['nullable', 'boolean'],
            'apporteur_nom_reseau' => ['nullable', 'string', 'max:255'],
            'apporteur_mode_acquisition' => ['nullable', 'array'],
            'apporteur_mode_acquisition.*' => ['string', 'in:reseau_personnel,reseau_professionnel,digital,publicite'],
            'apporteur_typologie_client' => ['nullable', 'array'],
            'apporteur_typologie_client.*' => ['string', 'in:particuliers,professionnels'],
            'apporteur_volume_mensuel_reco' => ['nullable', 'string', 'in:0_5,5_10,10_20,plus_20'],
            'apporteur_zone_geographique' => ['nullable', 'string', 'in:local,regional,national,international'],
            'apporteur_type_remuneration' => ['nullable', 'string', 'in:fixe,pourcentage,mixte'],
            'apporteur_remuneration_pourcentage' => ['nullable', 'numeric', 'between:0,100'],
            'apporteur_remuneration_fixe' => ['nullable', 'numeric', 'min:0'],
            'apporteur_declenchement_remuneration' => ['nullable', 'string', 'in:mise_en_relation,signature,encaissement'],
            'apporteur_remuneration_produit_reglemente' => ['nullable', 'boolean'],
            'apporteur_engagement_sans_conseil' => ['nullable', 'boolean'],
            'apporteur_engagement_sans_presentation' => ['nullable', 'boolean'],
            'apporteur_engagement_sans_encaissement' => ['nullable', 'boolean'],
            'apporteur_engagement_orientation' => ['nullable', 'boolean'],
            'apporteur_engagement_conformite' => ['nullable', 'boolean'],
        ]);

        abort_unless($creator->canCreateUserRole($validated['role']), 403);

        $isConseiller = $validated['role'] === 'conseiller';
        $isApporteur = $validated['role'] === 'apporteur';

        $newUser = User::create([
            'name' => trim($validated['prenom'].' '.$validated['nom']),
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'password' => Str::random(40),
            'role' => $validated['role'],
            'parent_id' => $creator->id,
            'activation_pending' => true,
            'perimetres' => $isConseiller ? ($validated['perimetres'] ?? []) : null,
            'habilitations' => $isConseiller ? ($validated['habilitations'] ?? []) : null,
            'numero_orias' => $isConseiller ? ($validated['numero_orias'] ?? null) : null,
            'apporteur_forme_juridique' => $isApporteur ? ($validated['apporteur_forme_juridique'] ?? null) : null,
            'apporteur_denomination_sociale' => $isApporteur ? ($validated['apporteur_denomination_sociale'] ?? null) : null,
            'apporteur_date_creation' => $isApporteur ? ($validated['apporteur_date_creation'] ?? null) : null,
            'apporteur_siren' => $isApporteur ? ($validated['apporteur_siren'] ?? null) : null,
            'apporteur_siret' => $isApporteur ? ($validated['apporteur_siret'] ?? null) : null,
            'apporteur_rcs_ville' => $isApporteur ? ($validated['apporteur_rcs_ville'] ?? null) : null,
            'apporteur_rcs_numero' => $isApporteur ? ($validated['apporteur_rcs_numero'] ?? null) : null,
            'apporteur_representant_legal' => $isApporteur ? ($validated['apporteur_representant_legal'] ?? null) : null,
            'apporteur_immatricule_orias' => $isApporteur ? ($validated['apporteur_immatricule_orias'] ?? null) : null,
            'apporteur_roles' => $isApporteur ? ($validated['apporteur_roles'] ?? []) : null,
            'apporteur_role_commentaire' => $isApporteur ? ($validated['apporteur_role_commentaire'] ?? null) : null,
            'apporteur_orias_numero' => $isApporteur ? ($validated['apporteur_orias_numero'] ?? null) : null,
            'apporteur_statut_reglemente' => $isApporteur ? ($validated['apporteur_statut_reglemente'] ?? []) : null,
            'apporteur_autorite_controle' => $isApporteur ? ($validated['apporteur_autorite_controle'] ?? []) : null,
            'apporteur_rcp' => $isApporteur ? ($validated['apporteur_rcp'] ?? null) : null,
            'apporteur_rcp_compagnie' => $isApporteur ? ($validated['apporteur_rcp_compagnie'] ?? null) : null,
            'apporteur_autre_reseau' => $isApporteur ? ($validated['apporteur_autre_reseau'] ?? null) : null,
            'apporteur_nom_reseau' => $isApporteur ? ($validated['apporteur_nom_reseau'] ?? null) : null,
            'apporteur_mode_acquisition' => $isApporteur ? ($validated['apporteur_mode_acquisition'] ?? []) : null,
            'apporteur_typologie_client' => $isApporteur ? ($validated['apporteur_typologie_client'] ?? []) : null,
            'apporteur_volume_mensuel_reco' => $isApporteur ? ($validated['apporteur_volume_mensuel_reco'] ?? null) : null,
            'apporteur_zone_geographique' => $isApporteur ? ($validated['apporteur_zone_geographique'] ?? null) : null,
            'apporteur_type_remuneration' => $isApporteur ? ($validated['apporteur_type_remuneration'] ?? null) : null,
            'apporteur_remuneration_pourcentage' => $isApporteur ? ($validated['apporteur_remuneration_pourcentage'] ?? null) : null,
            'apporteur_remuneration_fixe' => $isApporteur ? ($validated['apporteur_remuneration_fixe'] ?? null) : null,
            'apporteur_declenchement_remuneration' => $isApporteur ? ($validated['apporteur_declenchement_remuneration'] ?? null) : null,
            'apporteur_remuneration_produit_reglemente' => $isApporteur ? ($validated['apporteur_remuneration_produit_reglemente'] ?? null) : null,
            'apporteur_engagement_sans_conseil' => $isApporteur ? ($validated['apporteur_engagement_sans_conseil'] ?? false) : null,
            'apporteur_engagement_sans_presentation' => $isApporteur ? ($validated['apporteur_engagement_sans_presentation'] ?? false) : null,
            'apporteur_engagement_sans_encaissement' => $isApporteur ? ($validated['apporteur_engagement_sans_encaissement'] ?? false) : null,
            'apporteur_engagement_orientation' => $isApporteur ? ($validated['apporteur_engagement_orientation'] ?? false) : null,
            'apporteur_engagement_conformite' => $isApporteur ? ($validated['apporteur_engagement_conformite'] ?? false) : null,
        ]);

        Password::broker()->sendResetLink(['email' => $newUser->email]);

        return redirect()
            ->route('tenant.users.create')
            ->with('user_created', $newUser->name);
    }

    public function show(Request $request, User $user): View
    {
        $viewer = $request->user();

        $allowed = $user->id === $viewer->id
            || $user->parent_id === $viewer->id
            || ($user->parent && $user->parent->parent_id === $viewer->id);

        abort_unless($allowed, 403);

        $user->load(['parent', 'clients' => function ($query) {
            $query->orderBy('nom')->orderBy('prenom');
        }]);

        return view('tenant.users.show', ['profileUser' => $user]);
    }
}
