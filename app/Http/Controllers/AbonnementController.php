<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbonnementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->effectiveRole() === 'courtier', 403);

        return view('tenant.abonnement.index', [
            'nombreClientsActuel' => Client::query()->nonArchives()->count(),
            'nombreClientsMax' => tenant('abonnement_nombre_clients_max'),
            'abonnementModifieParNom' => tenant('abonnement_modifie_par_nom'),
            'abonnementModifieLe' => tenant('abonnement_modifie_le'),
            'paliers' => Tenant::PALIERS_ABONNEMENT,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($request->user()->effectiveRole() === 'courtier', 403);

        $validated = $request->validate([
            'abonnement_nombre_clients_max' => [
                'required', 'integer', 'in:'.implode(',', Tenant::PALIERS_ABONNEMENT),
            ],
        ]);

        $nombreClientsActuel = Client::query()->nonArchives()->count();

        if ($validated['abonnement_nombre_clients_max'] < $nombreClientsActuel) {
            return back()->withErrors([
                'abonnement_nombre_clients_max' => "Vous comptez actuellement {$nombreClientsActuel} clients : impossible de choisir un abonnement inférieur.",
            ]);
        }

        if ((int) tenant('abonnement_nombre_clients_max') !== $validated['abonnement_nombre_clients_max']) {
            tenant()->update([
                'abonnement_nombre_clients_max' => $validated['abonnement_nombre_clients_max'],
                'abonnement_modifie_par_nom' => $request->user()->name,
                'abonnement_modifie_le' => now(),
            ]);
        }

        return redirect()->route('tenant.abonnement.index')->with('status', 'Abonnement mis à jour.');
    }
}
