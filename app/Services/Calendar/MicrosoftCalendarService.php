<?php

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftCalendarService
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
            ->withHeaders(['Prefer' => 'outlook.timezone="UTC"'])
            ->get('https://graph.microsoft.com/v1.0/me/calendarView', [
                'startDateTime' => $from->toIso8601String(),
                'endDateTime' => $to->toIso8601String(),
                '$select' => 'subject,start,end,showAs',
                '$top' => 250,
            ]);

        if (! $response->successful()) {
            Log::warning('Microsoft Graph calendarView a échoué', [
                'connection_id' => $connection->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        }

        $evenements = $response->json('value', []);

        return collect($evenements)
            ->filter(fn (array $e) => ($e['showAs'] ?? 'busy') !== 'free')
            ->map(fn (array $e) => [
                'start' => Carbon::parse($e['start']['dateTime'], $e['start']['timeZone'] ?? 'UTC')->utc(),
                'end' => Carbon::parse($e['end']['dateTime'], $e['end']['timeZone'] ?? 'UTC')->utc(),
            ])->values()->all();
    }

    protected function ensureFreshToken(CalendarConnection $connection): void
    {
        if (! $connection->isExpired() || ! $connection->refresh_token) {
            return;
        }

        $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
            'client_id' => config('services.microsoft.client_id'),
            'client_secret' => config('services.microsoft.client_secret'),
            'refresh_token' => $connection->refresh_token,
            'grant_type' => 'refresh_token',
            'scope' => 'offline_access Calendars.Read',
        ]);

        if (! $response->successful()) {
            Log::warning('Rafraîchissement du token Microsoft échoué', [
                'connection_id' => $connection->id,
                'body' => $response->body(),
            ]);

            return;
        }

        $data = $response->json();

        $connection->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $connection->refresh_token,
            'token_expires_at' => now()->addSeconds($data['expires_in'] ?? 3600),
        ]);
    }
}
