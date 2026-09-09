<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FactureController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->effectiveRole() === 'apporteur', 403);

        $validated = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'fichier' => ['required', 'file', 'max:10240', 'mimes:pdf,jpg,jpeg,png'],
        ]);

        if (! empty($validated['client_id'])) {
            $client = \App\Models\Client::query()->find($validated['client_id']);
            abort_unless($client && (int) $client->apporteur_id === (int) $user->id, 403);
        }

        $fichier = $request->file('fichier');
        $path = $fichier->store('factures/' . $user->id, 'local');

        Facture::create([
            'apporteur_id' => $user->id,
            'client_id' => $validated['client_id'] ?? null,
            'fichier_path' => $path,
            'nom_original' => $fichier->getClientOriginalName(),
            'mime_type' => $fichier->getClientMimeType(),
            'taille' => $fichier->getSize(),
            'statut' => 'a_traiter',
        ]);

        return redirect()->route('tenant.portefeuille.index')->with('status', 'Facture envoyée.');
    }

    public function show(Request $request, Facture $facture): \Symfony\Component\HttpFoundation\Response
    {
        $user = $request->user();

        abort_unless((int) $facture->apporteur_id === (int) $user->id, 403);
        abort_unless(Storage::disk('local')->exists($facture->fichier_path), 404);

        return Storage::disk('local')->response($facture->fichier_path, $facture->nom_original);
    }

    public function destroy(Request $request, Facture $facture): RedirectResponse
    {
        $user = $request->user();

        abort_unless((int) $facture->apporteur_id === (int) $user->id, 403);

        Storage::disk('local')->delete($facture->fichier_path);
        $facture->delete();

        return redirect()->route('tenant.profil.edit')->with('status', 'facture-supprimee');
    }
}
