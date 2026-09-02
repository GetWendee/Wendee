<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\RendezVous;
use App\Services\Calendar\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RendezVousController extends Controller
{
    public function __construct(protected AvailabilityService $disponibilite)
    {
    }

    /**
     * Créneaux disponibles du conseiller connecté pour un jour donné (AJAX, popup).
     */
    public function disponibilites(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'duree' => ['nullable', 'integer', 'min:15', 'max:180'],
        ]);

        $jour = Carbon::parse($validated['date']);
        $duree = $validated['duree'] ?? 30;

        $creneaux = $this->disponibilite->creneauxDisponibles(Auth::user(), $jour, $duree);

        return response()->json(['creneaux' => $creneaux]);
    }

    public function store(Request $request, Client $client): RedirectResponse
    {
        $validated = $request->validate([
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'format' => ['nullable', 'string', 'in:visioconference,telephone,agence,domicile'],
            'sujet' => ['nullable', 'string', 'in:point_etape,bilan_patrimonial,signature_document,suivi_portefeuille,autre'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        RendezVous::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'titre' => 'Rendez-vous avec '.$client->prenom.' '.$client->nom,
            'format' => $validated['format'] ?? null,
            'sujet' => $validated['sujet'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'statut' => 'confirme',
        ]);

        return back()->with('status', 'Rendez-vous enregistré.');
    }
}
