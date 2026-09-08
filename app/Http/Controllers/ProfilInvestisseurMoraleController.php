<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Profil investisseur d'une personne morale : pas de questionnaire scoré
 * comme pour la personne physique (ProfilInvestisseurController), des
 * champs déclaratifs directs. Voir claude/kyc-personne-morale.md.
 */
class ProfilInvestisseurMoraleController extends Controller
{
    public function edit(Client $client): View|RedirectResponse
    {
        if (! $client->estMorale()) {
            return redirect()->route('tenant.clients.profil.edit', $client);
        }

        $client->load('profilInvestisseurMorale');

        return view('tenant.clients.profil-investisseur-morale', [
            'client' => $client,
        ]);
    }

    public function update(Request $request, Client $client): RedirectResponse
    {
        abort_unless($client->estMorale(), 404);

        $validated = $request->validate([
            'profil_risque' => ['nullable', 'integer', 'min:1', 'max:7'],

            'objectifs' => ['nullable', 'array'],
            'objectifs.*.libelle' => ['nullable', 'string', 'max:255'],
            'objectifs.*.horizon_annees' => ['nullable', 'integer', 'min:0'],

            'esg_interesse' => ['nullable', 'boolean'],
            'esg_taxonomie' => ['nullable', 'boolean'],
            'esg_sfdr' => ['nullable', 'boolean'],
            'esg_pai' => ['nullable', 'boolean'],
            'esg_accepte_performance_moindre' => ['nullable', 'boolean'],
            'esg_objectif_pourcentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'esg_objectif_libre' => ['nullable', 'string'],
            'esg_profil_investissement' => ['nullable', 'integer', 'min:1', 'max:7'],
            'esg_profil_patrimoine_global' => ['nullable', 'integer', 'min:1', 'max:7'],
            'esg_patrimoine_global_pourcentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'esg_patrimoine_global_libre' => ['nullable', 'string'],
            'esg_indicateurs_environnementaux' => ['nullable', 'array'],
            'esg_indicateurs_environnementaux.*' => ['string'],
            'esg_indicateurs_sociaux' => ['nullable', 'array'],
            'esg_indicateurs_sociaux.*' => ['string'],
            'esg_objectifs_besoins_horizon' => ['nullable', 'string'],

            'accepte_cgu' => ['nullable', 'boolean'],
        ]);

        $validated['esg_interesse'] = $request->boolean('esg_interesse');
        $validated['esg_taxonomie'] = $request->boolean('esg_taxonomie');
        $validated['esg_sfdr'] = $request->boolean('esg_sfdr');
        $validated['esg_pai'] = $request->boolean('esg_pai');
        $validated['esg_accepte_performance_moindre'] = $request->boolean('esg_accepte_performance_moindre');

        if ($request->boolean('accepte_cgu')) {
            $validated['accepte_cgu'] = true;
            $validated['signe_le'] = now();
        }

        $objectifs = array_values(array_filter($validated['objectifs'] ?? [], function ($objectif) {
            return ! empty($objectif['libelle']);
        }));
        $validated['objectifs'] = $objectifs;

        $client->profilInvestisseurMorale()->updateOrCreate([], $validated);

        return redirect()
            ->route('tenant.clients.profil-investisseur-morale.edit', $client)
            ->with('status', 'Profil investisseur société enregistré.');
    }
}
