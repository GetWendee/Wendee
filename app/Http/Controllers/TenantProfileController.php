<?php

namespace App\Http\Controllers;

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
        return view('tenant.profil', [
            'user' => $request->user(),
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
}
