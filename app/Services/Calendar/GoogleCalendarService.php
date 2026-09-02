<?php

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    /**
     * Renvoie les plages occupées (busy) entre $from et $to pour cette connexion.
     * Rafraîchit le token d'accès si besoin.
     *
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    public function getBusyPeriods(CalendarConnection $connection, Carbon $from, Carbon $to): array
    {
        $this->ensureFreshToken($connection);

        $response = Http::withToken($connection->access_token)
            ->post('https://www.googleapis.com/calendar/v3/freeBusy', [
                'timeMin' => $from->toRfc3339String(),
                'timeMax' => $to->toRfc3339String(),
                'items' => [
                    ['id' => $connection->calendar_id ?: 'primary'],
                ],
            ]);

        if (! $response->successful()) {
            Log::warning('Google Calendar freeBusy a échoué', [
                'connection_id' => $connection->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $calendarId = $connection->calendar_id ?: 'primary';
        $busy = $response->json("calendars.{$calendarId}.busy", []);

        return collect($busy)->map(fn (array $periode) => [
            'start' => Carbon::parse($periode['start']),
            'end' => Carbon::parse($periode['end']),
        ])->all();
    }

    /**
     * Crée un événement dans le calendrier Google du conseiller.
     * Renvoie l'ID de l'événement créé, ou null en cas d'échec.
     */
    public function createEvent(CalendarConnection $connection, string $titre, Carbon $debut, Carbon $fin, ?string $description = null): ?string
    {
        $this->ensureFreshToken($connection);

        $calendarId = $connection->calendar_id ?: 'primary';

        $response = Http::withToken($connection->access_token)
            ->post("https://www.googleapis.com/calendar/v3/calendars/{$calendarId}/events", [
                'summary' => $titre,
                'description' => $description,
                'start' => ['dateTime' => $debut->toRfc3339String()],
                'end' => ['dateTime' => $fin->toRfc3339String()],
            ]);

        if (! $response->successful()) {
            Log::warning('Google Calendar création événement a échoué', [
                'connection_id' => $connection->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return $response->json('id');
    }

    protected function ensureFreshToken(CalendarConnection $connection): void
    {
        if (! $connection->isExpired() || ! $connection->refresh_token) {
            return;
        }

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'refresh_token' => $connection->refresh_token,
            'grant_type' => 'refresh_token',
        ]);

        if (! $response->successful()) {
            Log::warning('Rafraîchissement du token Google échoué', [
                'connection_id' => $connection->id,
                'body' => $response->body(),
            ]);

            return;
        }

        $data = $response->json();

        $connection->update([
            'access_token' => $data['access_token'],
            'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
        ]);
    }
}
