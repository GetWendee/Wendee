<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\CabinetProfile;
use App\Models\User;
use App\Services\Sirene\SireneService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Notifications\WelcomeCourtierNotification;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CabinetController extends Controller
{
    public function index(): View
    {
        $cabinets = Tenant::with('domains')->orderByDesc('created_at')->get();

        return view('cabinets.index', ['cabinets' => $cabinets]);
    }

    public function create(Request $request): View
    {
        return view('cabinets.create', [
            'sireneData' => $request->session()->get('cabinet.sirene'),
        ]);
    }

    /**
     * Recherche un établissement dans SIRENE.
     *
     * Cette méthode ne crée aucun cabinet.
     * Elle sert uniquement à vérifier et présenter les données
     * retournées par l'INSEE avant la création du tenant.
     */
    public function searchSirene(
        Request $request,
        SireneService $sirene
    ): RedirectResponse
    {
        $validated = $request->validate([
            'siret' => [
                'required',
                'string',
                'regex:/^\d{14}$/',
            ],
        ]);

        try {

            $sireneData = $sirene->findBySiret(
                $validated['siret']
            );

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'siret' => $e->getMessage(),
                ]);
        }

        if (! $sireneData) {

            return back()
                ->withInput()
                ->withErrors([
                    'siret' =>
                        'Aucun établissement correspondant à ce SIRET n’a été trouvé dans SIRENE.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CONTRÔLE COHÉRENCE
        |--------------------------------------------------------------------------
        */

        if (
            ($sireneData['siret'] ?? null)
            !== $validated['siret']
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'siret' =>
                        'Le SIRET retourné par SIRENE ne correspond pas au SIRET saisi.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CONTRÔLE ÉTABLISSEMENT ACTIF
        |--------------------------------------------------------------------------
        */

        if (
            isset($sireneData['etat_administratif']) &&
            $sireneData['etat_administratif'] !== 'A'
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'siret' =>
                        'Cet établissement n’est pas indiqué comme actif dans SIRENE.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | STOCKAGE TEMPORAIRE SERVEUR
        |--------------------------------------------------------------------------
        */

        $request->session()->put(
            'cabinet.sirene',
            $sireneData
        );

        return back()
            ->with(
                'sirene_success',
                'Établissement identifié avec succès.'
            );
    }

    public function store(
        Request $request,
        SireneService $sirene
    ): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required', 'string', 'max:63', 'alpha_dash', 'lowercase',
                'unique:tenants,id',
            ],
            'courtier_name' => ['required', 'string', 'max:255'],
            'courtier_email' => ['required', 'string', 'email', 'max:255'],

            'siret' => [
                'required',
                'string',
                'regex:/^\d{14}$/',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | VÉRIFICATION SIRENE
        |--------------------------------------------------------------------------
        */

        try {

            $sireneData = $sirene->findBySiret(
                $validated['siret']
            );

        } catch (\Throwable $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'siret' => $e->getMessage(),
                ]);
        }

        if (! $sireneData) {

            return back()
                ->withInput()
                ->withErrors([
                    'siret' => 'Aucun établissement correspondant à ce SIRET n’a été trouvé dans SIRENE.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CONTRÔLE COHÉRENCE SIRET
        |--------------------------------------------------------------------------
        */

        $siretSirene = preg_replace(
            '/\D+/',
            '',
            (string) ($sireneData['siret'] ?? '')
        );

        if ($siretSirene !== $validated['siret']) {

            return back()
                ->withInput()
                ->withErrors([
                    'siret' => 'Le SIRET retourné par SIRENE ne correspond pas au SIRET saisi.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CONTRÔLE ÉTABLISSEMENT ACTIF
        |--------------------------------------------------------------------------
        */

        if (
            isset($sireneData['etat_administratif']) &&
            $sireneData['etat_administratif'] !== 'A'
        ) {

            return back()
                ->withInput()
                ->withErrors([
                    'siret' => 'Cet établissement n’est pas indiqué comme actif dans SIRENE.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | CRÉATION DU TENANT
        |--------------------------------------------------------------------------
        */

        $tenant = Tenant::create([
            'id' => $validated['slug'],
            'name' => $sireneData['raison_sociale'] ?? $validated['name'],
            'siren' => $sireneData['siren'] ?? null,
            'siret' => $sireneData['siret'] ?? $validated['siret'],
            'raison_sociale' => $sireneData['raison_sociale'] ?? $validated['name'],
            'forme_juridique' => $sireneData['forme_juridique'] ?? null,
            'code_ape' => $sireneData['code_ape'] ?? null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | DOMAINE
        |--------------------------------------------------------------------------
        */

        $tenant->domains()->create([
            'domain' => $validated['slug'].'.wendee.fr',
        ]);

        /*
        |--------------------------------------------------------------------------
        | INITIALISATION DE LA BASE TENANT
        |--------------------------------------------------------------------------
        */

        [$courtier, $tokenReset] = $tenant->run(function () use (
            $validated,
            $sireneData
        ) {

            /*
            |----------------------------------------------------------------------
            | PROFIL DU CABINET
            |----------------------------------------------------------------------
            */

            CabinetProfile::create([
                'nom_commercial' => $validated['name'],
                'raison_sociale' => $sireneData['raison_sociale'] ?? null,

                'forme_juridique' => $sireneData['forme_juridique'] ?? null,

                'siren' => $sireneData['siren'] ?? null,
                'siret' => $sireneData['siret'] ?? null,

                'adresse' => $sireneData['adresse'] ?? null,
                'code_postal' => $sireneData['code_postal'] ?? null,
                'ville' => $sireneData['ville'] ?? null,
                'pays' => 'France',

                'code_ape' => $sireneData['code_ape'] ?? null,
                'libelle_ape' => $sireneData['libelle_ape'] ?? null,
                'activite_principale' => $sireneData['activite_principale'] ?? null,
                'etat_administratif' => $sireneData['etat_administratif'] ?? null,
                'date_creation' => $sireneData['date_creation'] ?? null,
                'enseigne' => $sireneData['enseigne'] ?? null,
                'nom_unite_legale' => $sireneData['nom_unite_legale'] ?? null,

                'donnees_sirene' => $sireneData['donnees_sirene'] ?? null,
            ]);

            /*
            |----------------------------------------------------------------------
            | PREMIER UTILISATEUR
            |---------------------------------------------------------------------- 
            */

            $user = User::create([
                'name' => $validated['courtier_name'],
                'email' => $validated['courtier_email'],
                'password' => Str::random(40),
                'role' => 'courtier',
                'activation_pending' => true,
            ]);

            $token = Password::createToken($user);

            \Illuminate\Support\Facades\Mail::send(
                'emails.cabinet-created',
                [
                    'cabinetName' => $validated['name'],
                    'courtierName' => $validated['courtier_name'],
                    'courtierEmail' => $validated['courtier_email'],
                    'siret' => $sireneData['siret'] ?? $validated['siret'],
                    'siren' => $sireneData['siren'] ?? null,
                    'raisonSociale' => $sireneData['raison_sociale'] ?? null,
                    'codeApe' => $sireneData['code_ape'] ?? null,
                    'domain' => $validated['slug'].'.wendee.fr',
                ],
                function ($message) {
                    $message
                        ->to('contact@getwendee.fr')
                        ->subject('Nouveau cabinet créé sur Wendee');
                }
            );

            return [$user, $token];
        });

        $courtier->notifyNow(
            new WelcomeCourtierNotification(
                $tokenReset,
                $validated['slug'].'.wendee.fr',
                $validated['name']
            )
        );

        return redirect()->route('cabinets.index')->with('status', [
            'cabinet' => $validated['name'],
            'domain' => $validated['slug'].'.wendee.fr',
            'courtier_email' => $validated['courtier_email'],
        ]);
    }

    public function edit(Tenant $tenant): View
    {
        return view('cabinets.edit', ['cabinet' => $tenant]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
        $tenant->update(['name' => $validated['name']]);
        return redirect()->route('cabinets.index')->with('status_simple', 'Cabinet mis à jour.');
    }

    public function toggleActif(Tenant $tenant): RedirectResponse
    {
        $nouvelEtat = ! ($tenant->actif ?? true);
        $tenant->update(['actif' => $nouvelEtat]);
        return redirect()->route('cabinets.index')->with(
            'status_simple',
            $nouvelEtat ? 'Cabinet réactivé.' : 'Cabinet désactivé.'
        );
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        $nom = $tenant->name;
        $tenant->delete();
        return redirect()->route('cabinets.index')->with(
            'status_simple',
            "Cabinet « {$nom} » supprimé définitivement (base de données comprise)."
        );
    }
}
