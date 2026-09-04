<?php

namespace App\Http\Controllers;

use App\Models\CalendarConnection;
use App\Models\Client;
use App\Models\RendezVous;
use App\Models\User;
use App\Services\Calendar\AvailabilityService;
use App\Services\Calendar\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class RendezVousController extends Controller
{
    private const PALETTE_CONSEILLERS = ['#f40087', '#151515', '#2f7a4f', '#b98a2f', '#3f6fb9', '#8a4fb9', '#c94f4f', '#4fa8a8'];

    private const HEURE_DEBUT = 7;

    private const HEURE_FIN = 21;

    public function __construct(
        protected AvailabilityService $disponibilite,
        protected GoogleCalendarService $googleCalendar,
    ) {
    }

    /**
     * Vue agenda (jour / semaine / mois) : tous les RDV du cabinet en mode
     * courtier, uniquement les siens en mode conseiller.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        abort_unless($user && in_array($user->effectiveRole(), ['courtier', 'conseiller'], true), 403);

        $vue = in_array($request->query('vue'), ['jour', 'semaine', 'mois'], true) ? $request->query('vue') : 'semaine';

        try {
            $dateRef = $request->query('date') ? Carbon::parse($request->query('date'))->startOfDay() : today();
        } catch (\Exception) {
            $dateRef = today();
        }

        [$debutAffichage, $finAffichage] = $this->bornesVue($vue, $dateRef);

        $requeteRdv = RendezVous::query()
            ->with(['client', 'conseiller'])
            ->where('statut', '!=', 'annule')
            ->whereBetween('starts_at', [$debutAffichage, $finAffichage->copy()->endOfDay()])
            ->orderBy('starts_at');

        if ($user->effectiveRole() === 'conseiller') {
            $requeteRdv->where('user_id', $user->id);
        }

        $rdvs = $requeteRdv->get();

        // Créneaux déjà pris sur les calendriers externes connectés (Google/Outlook),
        // hors RDV Wendee, pour matérialiser les indisponibilités (ex : médecin).
        // Non calculé en vue mois : trop dense pour être lisible et coûteux en appels API.
        $indisposParConseiller = collect();

        if ($vue !== 'mois') {
            $conseillersAConsiderer = $user->effectiveRole() === 'courtier'
                ? User::whereIn('role', ['courtier', 'conseiller'])->get()
                : collect([$user]);

            foreach ($conseillersAConsiderer as $conseiller) {
                $plages = $this->disponibilite->busyExternes($conseiller, $debutAffichage, $finAffichage->copy()->endOfDay());

                if (! empty($plages)) {
                    $indisposParConseiller->put($conseiller->id, ['conseiller' => $conseiller, 'plages' => $plages]);
                }
            }
        }

        $conseillers = $rdvs->pluck('conseiller')->filter()
            ->concat($indisposParConseiller->pluck('conseiller'))
            ->unique('id')
            ->values();

        $couleurs = $conseillers->mapWithKeys(fn ($c, $i) => [$c->id => self::PALETTE_CONSEILLERS[$i % count(self::PALETTE_CONSEILLERS)]]);

        $joursGrille = collect();

        for ($jour = $debutAffichage->copy(); $jour->lte($finAffichage); $jour->addDay()) {
            $rdvsDuJour = $rdvs->filter(fn (RendezVous $r) => $r->starts_at->isSameDay($jour))->values();

            $joursGrille->push([
                'date' => $jour->copy(),
                'evenements' => $vue === 'mois' ? $rdvsDuJour : $this->layoutJournee($rdvsDuJour),
                'indispos' => $vue === 'mois' ? [] : $this->layoutIndispos($indisposParConseiller, $jour->copy()),
                'total' => $rdvsDuJour->count(),
            ]);
        }

        $connexionsCalendrier = CalendarConnection::where('user_id', $user->id)->get()->keyBy('provider');

        return view('tenant.rendez-vous.index', [
            'vue' => $vue,
            'dateRef' => $dateRef,
            'debutAffichage' => $debutAffichage,
            'finAffichage' => $finAffichage,
            'joursGrille' => $joursGrille,
            'conseillers' => $conseillers,
            'couleurs' => $couleurs,
            'datePrecedente' => $this->decalerDate($vue, $dateRef, -1),
            'dateSuivante' => $this->decalerDate($vue, $dateRef, 1),
            'connexionsCalendrier' => $connexionsCalendrier,
            'estCourtier' => $user->effectiveRole() === 'courtier',
            'heureDebut' => self::HEURE_DEBUT,
            'heureFin' => self::HEURE_FIN,
        ]);
    }

    private function bornesVue(string $vue, Carbon $dateRef): array
    {
        return match ($vue) {
            'jour' => [$dateRef->copy()->startOfDay(), $dateRef->copy()->startOfDay()],
            'mois' => [
                $dateRef->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY),
                $dateRef->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY),
            ],
            default => [
                $dateRef->copy()->startOfWeek(Carbon::MONDAY),
                $dateRef->copy()->endOfWeek(Carbon::SUNDAY),
            ],
        };
    }

    private function decalerDate(string $vue, Carbon $dateRef, int $sens): Carbon
    {
        return match ($vue) {
            'jour' => $dateRef->copy()->addDays($sens),
            'mois' => $dateRef->copy()->addMonths($sens),
            default => $dateRef->copy()->addWeeks($sens),
        };
    }

    /**
     * Positionne les RDV d'une journée dans une grille horaire (top/hauteur
     * en %), avec répartition en colonnes ("lanes") pour les RDV qui se
     * chevauchent.
     */
    private function layoutJournee(Collection $rdvsDuJour): array
    {
        $totalMinutes = (self::HEURE_FIN - self::HEURE_DEBUT) * 60;

        $evenements = $rdvsDuJour->map(function (RendezVous $r) use ($totalMinutes) {
            $debutGrille = $r->starts_at->copy()->startOfDay()->addHours(self::HEURE_DEBUT);
            $minutesDebut = $debutGrille->diffInMinutes($r->starts_at, false);
            $dureeMinutes = max(15, $r->starts_at->diffInMinutes($r->ends_at));

            return [
                'rdv' => $r,
                'debut_minutes' => max(0, min($totalMinutes, $minutesDebut)),
                'fin_minutes' => max(0, min($totalMinutes, $minutesDebut + $dureeMinutes)),
            ];
        })->sortBy('debut_minutes')->values();

        $clusters = [];
        $clusterCourant = [];
        $finClusterCourant = -1;

        foreach ($evenements as $evenement) {
            if (empty($clusterCourant) || $evenement['debut_minutes'] < $finClusterCourant) {
                $clusterCourant[] = $evenement;
                $finClusterCourant = max($finClusterCourant, $evenement['fin_minutes']);
            } else {
                $clusters[] = $clusterCourant;
                $clusterCourant = [$evenement];
                $finClusterCourant = $evenement['fin_minutes'];
            }
        }

        if (! empty($clusterCourant)) {
            $clusters[] = $clusterCourant;
        }

        $resultat = [];

        foreach ($clusters as $cluster) {
            $lanes = [];
            $clusterResultat = [];

            foreach ($cluster as $evenement) {
                $lanePlacee = null;

                foreach ($lanes as $index => $finLane) {
                    if ($evenement['debut_minutes'] >= $finLane) {
                        $lanes[$index] = $evenement['fin_minutes'];
                        $lanePlacee = $index;
                        break;
                    }
                }

                if ($lanePlacee === null) {
                    $lanePlacee = count($lanes);
                    $lanes[] = $evenement['fin_minutes'];
                }

                $evenement['lane'] = $lanePlacee;
                $clusterResultat[] = $evenement;
            }

            $nombreLanes = count($lanes);

            foreach ($clusterResultat as $evenement) {
                $evenement['lane_count'] = $nombreLanes;
                $resultat[] = $evenement;
            }
        }

        return array_map(function ($e) use ($totalMinutes) {
            $e['top_pct'] = ($e['debut_minutes'] / $totalMinutes) * 100;
            $e['height_pct'] = max(2, (($e['fin_minutes'] - $e['debut_minutes']) / $totalMinutes) * 100);
            $e['width_pct'] = 100 / $e['lane_count'];
            $e['left_pct'] = $e['lane'] * $e['width_pct'];

            return $e;
        }, $resultat);
    }

    /**
     * Positionne les plages "indisponible" (agendas externes connectés, hors
     * Wendee) d'une journée dans la même grille horaire que les RDV. Pleine
     * largeur de colonne, pas de gestion de lanes : c'est un simple indicateur
     * en arrière-plan, pas un créneau réservable dans Wendee.
     *
     * @return array<int, array{conseiller_id: int, top_pct: float, height_pct: float}>
     */
    private function layoutIndispos(Collection $indisposParConseiller, Carbon $jour): array
    {
        $totalMinutes = (self::HEURE_FIN - self::HEURE_DEBUT) * 60;
        $debutGrille = $jour->copy()->startOfDay()->addHours(self::HEURE_DEBUT);
        $finGrille = $jour->copy()->startOfDay()->addHours(self::HEURE_FIN);

        $resultat = [];

        foreach ($indisposParConseiller as $conseillerId => $donnees) {
            foreach ($donnees['plages'] as $plage) {
                if ($plage['end']->lte($debutGrille) || $plage['start']->gte($finGrille)) {
                    continue;
                }

                $debut = $plage['start']->lt($debutGrille) ? $debutGrille : $plage['start'];
                $fin = $plage['end']->gt($finGrille) ? $finGrille : $plage['end'];

                $minutesDebut = $debutGrille->diffInMinutes($debut, false);
                $minutesFin = $debutGrille->diffInMinutes($fin, false);

                $resultat[] = [
                    'conseiller_id' => $conseillerId,
                    'top_pct' => ($minutesDebut / $totalMinutes) * 100,
                    'height_pct' => max(1.5, (($minutesFin - $minutesDebut) / $totalMinutes) * 100),
                ];
            }
        }

        return $resultat;
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

        $debut = Carbon::parse($validated['starts_at']);
        $fin = Carbon::parse($validated['ends_at']);

        // Vérification serveur : la liste de créneaux du formulaire peut être
        // périmée (deux requêtes rapprochées, onglet resté ouvert). On ne se
        // fie jamais à ce que le client affiche pour autoriser l'écriture.
        if (! $this->disponibilite->creneauLibre(Auth::user(), $debut, $fin)) {
            abort(409, "Ce créneau vient d'être pris, choisis-en un autre.");
        }

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

        $debut = Carbon::parse($validated['starts_at']);
        $fin = Carbon::parse($validated['ends_at']);

        if (! $this->disponibilite->creneauLibre(Auth::user(), $debut, $fin, $rendezVous->id)) {
            abort(409, "Ce créneau vient d'être pris, choisis-en un autre.");
        }

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
