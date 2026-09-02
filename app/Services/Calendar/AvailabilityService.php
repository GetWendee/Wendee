<?php

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use App\Models\RendezVous;
use App\Models\User;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Heures d'ouverture par défaut. À terme : configurable par cabinet/conseiller.
     */
    protected int $heureDebut = 9;
    protected int $heureFin = 18;

    /**
     * Délai minimum avant un RDV (pas de créneau dans l'heure qui vient).
     */
    protected int $delaiMinimumMinutes = 60;

    public function __construct(
        protected GoogleCalendarService $google,
        protected MicrosoftCalendarService $microsoft,
    ) {
    }

    /**
     * Calcule les créneaux disponibles pour un conseiller sur une journée donnée.
     *
     * @return array<int, array{start: string, end: string}>
     */
    public function creneauxDisponibles(User $conseiller, Carbon $jour, int $dureeMinutes = 30): array
    {
        $debutJournee = $jour->copy()->setTime($this->heureDebut, 0);
        $finJournee = $jour->copy()->setTime($this->heureFin, 0);

        if ($debutJournee->isWeekend()) {
            return [];
        }

        $plagesOccupees = $this->plagesOccupees($conseiller, $debutJournee, $finJournee);

        $seuilMinimum = now()->addMinutes($this->delaiMinimumMinutes);

        $creneaux = [];
        $curseur = $debutJournee->copy();

        while ($curseur->copy()->addMinutes($dureeMinutes)->lte($finJournee)) {
            $finCreneau = $curseur->copy()->addMinutes($dureeMinutes);

            $disponible = $curseur->gte($seuilMinimum)
                && ! $this->chevaucheUnePlage($curseur, $finCreneau, $plagesOccupees);

            if ($disponible) {
                $creneaux[] = [
                    'start' => $curseur->toIso8601String(),
                    'end' => $finCreneau->toIso8601String(),
                ];
            }

            $curseur->addMinutes($dureeMinutes);
        }

        return $creneaux;
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    protected function plagesOccupees(User $conseiller, Carbon $from, Carbon $to): array
    {
        $plages = [];

        foreach (CalendarConnection::where('user_id', $conseiller->id)->get() as $connection) {
            $plages = array_merge($plages, match ($connection->provider) {
                'google' => $this->google->getBusyPeriods($connection, $from, $to),
                'microsoft' => $this->microsoft->getBusyPeriods($connection, $from, $to),
                default => [],
            });
        }

        // Les RDV déjà pris dans Wendee comptent aussi comme occupés,
        // même si la synchro calendrier externe a du retard.
        $rdvExistants = RendezVous::where('user_id', $conseiller->id)
            ->where('statut', 'confirme')
            ->whereBetween('starts_at', [$from, $to])
            ->get();

        foreach ($rdvExistants as $rdv) {
            $plages[] = ['start' => $rdv->starts_at, 'end' => $rdv->ends_at];
        }

        return $plages;
    }

    protected function chevaucheUnePlage(Carbon $debut, Carbon $fin, array $plages): bool
    {
        foreach ($plages as $plage) {
            if ($debut->lt($plage['end']) && $fin->gt($plage['start'])) {
                return true;
            }
        }

        return false;
    }
}
