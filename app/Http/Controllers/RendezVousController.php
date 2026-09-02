<?php

namespace App\Http\Controllers;

use App\Models\CalendarConnection;
use App\Models\Client;
use App\Models\RendezVous;
use App\Services\Calendar\AvailabilityService;
use App\Services\Calendar\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RendezVousController extends Controller
{
    public function __construct(
        protected AvailabilityService $disponibilite,
        protected GoogleCalendarService $googleCalendar,
    ) {
    }

    /**
     * Créneaux disponibles du conseiller connecté pour un jour donné (AJAX, popup).
     */
    public function disponibilites(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'duree' => ['nullable', 'integer', 'min:15', 'max:180'],
            'exclure' => ['nullable', 'integer'],
        ]);

        $jour = Carbon::parse($validated['date']);
        $duree = $validated['duree'] ?? 30;

        $creneaux = $this->disponibilite->creneauxDisponibles(Auth::user(), $jour, $duree, $validated['exclure'] ?? null);

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

        $rendezVous = RendezVous::create([
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

        $connexionGoogle = CalendarConnection::where('user_id', Auth::id())
            ->where('provider', 'google')
            ->first();

        if ($connexionGoogle) {
            $googleEventId = $this->googleCalendar->createEvent(
                $connexionGoogle,
                $rendezVous->titre,
                Carbon::parse($rendezVous->starts_at),
                Carbon::parse($rendezVous->ends_at),
                $rendezVous->notes,
                $client->email
            );

            if ($googleEventId) {
                $rendezVous->update([
                    'calendar_provider' => 'google',
                    'external_event_id' => $googleEventId,
                ]);
            }
        }

        return back()->with('status', 'Rendez-vous enregistré.');
    }

    public function annuler(RendezVous $rendezVous): RedirectResponse
    {
        abort_unless($rendezVous->user_id === Auth::id(), 403);

        if ($rendezVous->calendar_provider === 'google' && $rendezVous->external_event_id) {
            $connexionGoogle = CalendarConnection::where('user_id', Auth::id())
                ->where('provider', 'google')
                ->first();

            if ($connexionGoogle) {
                $this->googleCalendar->deleteEvent($connexionGoogle, $rendezVous->external_event_id);
            }
        }

        $rendezVous->update(['statut' => 'annule']);

        return back()->with('status', 'Rendez-vous annulé.');
    }

    public function decaler(Request $request, RendezVous $rendezVous): RedirectResponse
    {
        abort_unless($rendezVous->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ]);

        $rendezVous->update([
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
        ]);

        if ($rendezVous->calendar_provider === 'google' && $rendezVous->external_event_id) {
            $connexionGoogle = CalendarConnection::where('user_id', Auth::id())
                ->where('provider', 'google')
                ->first();

            if ($connexionGoogle) {
                $this->googleCalendar->updateEvent(
                    $connexionGoogle,
                    $rendezVous->external_event_id,
                    Carbon::parse($validated['starts_at']),
                    Carbon::parse($validated['ends_at'])
                );
            }
        }

        return back()->with('status', 'Rendez-vous décalé.');
    }
}
