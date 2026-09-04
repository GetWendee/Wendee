<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientDocumentController extends Controller
{
    public const TYPES = [
        'piece_identite' => ['label' => "Mes pièces d'identité", 'sub' => "Carte d'identité, passeport, carte de résident", 'categorie' => 'personnels'],
        'justificatif_domicile' => ['label' => 'Mes justificatifs de domicile', 'sub' => 'Documents liés à votre adresse', 'categorie' => 'personnels'],
        'avis_imposition' => ['label' => "Avis d'imposition", 'sub' => 'Dernier document fiscal sur vos revenus', 'categorie' => 'personnels'],
        'avis_ifi' => ['label' => 'Avis IFI', 'sub' => 'Document fiscal de votre patrimoine immobilier', 'categorie' => 'personnels'],
        'permis_conduire' => ['label' => 'Permis de conduire', 'sub' => 'Assurance véhicule', 'categorie' => 'mandats'],
        'carte_grise' => ['label' => 'Cartes grises', 'sub' => 'Assurance véhicule', 'categorie' => 'mandats'],
        'releve_information_vehicule' => ['label' => "Relevés d'information", 'sub' => 'Assurance véhicule', 'categorie' => 'mandats'],
        'releve_information_habitation' => ['label' => "Relevés d'information", 'sub' => 'Assurance habitation', 'categorie' => 'mandats'],
    ];

    public function store(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $existant = $client->documents()->where('type', $validated['type'])->first();

        if ($existant) {
            Storage::disk('local')->delete($existant->chemin);
        }

        $fichier = $request->file('fichier');
        $chemin = $fichier->store('client-documents/' . $client->id, 'local');

        $client->documents()->updateOrCreate(
            ['type' => $validated['type']],
            [
                'chemin' => $chemin,
                'nom_original' => $fichier->getClientOriginalName(),
                'taille' => $fichier->getSize(),
            ]
        );

        return redirect()
            ->route('tenant.clients.conformites-clients', $client)
            ->with('status_simple', 'Document ajouté.');
    }

    public function download(Client $client, string $type): StreamedResponse
    {
        $document = $client->documents()->where('type', $type)->firstOrFail();

        return Storage::disk('local')->download($document->chemin, $document->nom_original);
    }

    public function destroy(Client $client, string $type): RedirectResponse
    {
        $document = $client->documents()->where('type', $type)->firstOrFail();

        Storage::disk('local')->delete($document->chemin);
        $document->delete();

        return redirect()
            ->route('tenant.clients.conformites-clients', $client)
            ->with('status_simple', 'Document supprimé.');
    }
}
