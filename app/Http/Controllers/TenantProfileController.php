<?php

namespace App\Http\Controllers;

use App\Models\DemandeChangementEmail;
use App\Notifications\ConfirmationChangementEmailNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantProfileController extends Controller
{
    /**
     * Affiche le formulaire de profil du user tenant connecté
     * (nom, email). Le mot de passe est géré par TenantAuth\PasswordController.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $factures = $user->effectiveRole() === 'apporteur'
            ? \App\Models\Facture::query()
                ->where('apporteur_id', $user->id)
                ->with('client')
                ->latest()
                ->get()
            : null;

        return view('tenant.profil', [
            'user' => $user,
            'factures' => $factures,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($request->user()->id),
            ],
        ]);

        $request->user()->update($validated);

        return Redirect::route('tenant.profil.edit')->with('status', 'profil-mis-a-jour');
    }

    public function demanderChangementEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'mot_de_passe_actuel' => ['required', 'current_password'],
            'nouvel_email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $demande = DemandeChangementEmail::create([
            'user_id' => $user->id,
            'ancien_email' => $user->email,
            'nouvel_email' => $validated['nouvel_email'],
            'statut' => 'en_attente',
            'expire_le' => now()->addHours(48),
        ]);

        $urlValider = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'tenant.profil.email.valider',
            now()->addHours(48),
            ['demande' => $demande->id]
        );

        $urlRefuser = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'tenant.profil.email.refuser',
            now()->addHours(48),
            ['demande' => $demande->id]
        );

        $user->notify(new ConfirmationChangementEmailNotification($demande, $urlValider, $urlRefuser));

        return Redirect::route('tenant.profil.edit')->with('status', 'demande-changement-email-envoyee');
    }

    public function validerChangementEmail(Request $request, DemandeChangementEmail $demande): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        if (! $demande->estEnAttente()) {
            return redirect()->route('tenant.login')->with('status', "Cette demande n'est plus valide.");
        }

        $demande->user->update(['email' => $demande->nouvel_email]);
        $demande->update(['statut' => 'validee']);

        return redirect()->route('tenant.login')->with('status', 'Votre adresse e-mail a bien été mise à jour. Connectez-vous avec votre nouvelle adresse.');
    }

    public function refuserChangementEmail(Request $request, DemandeChangementEmail $demande): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        if ($demande->statut === 'en_attente') {
            $demande->update(['statut' => 'refusee']);
            $demande->user->update(['bloque_le' => now()]);
        }

        return redirect()->route('tenant.login')->with('status', 'Ce changement a été refusé, le compte a été bloqué par mesure de sécurité. Contactez votre conseiller.');
    }

    public function statutCompte(Request $request): JsonResponse
    {
        return response()->json([
            'bloque' => (bool) $request->user()?->bloque_le,
        ]);
    }
}
