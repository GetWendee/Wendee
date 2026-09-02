<?php

namespace App\Http\Controllers;

use App\Models\CalendarConnection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class CalendarConnectionController extends Controller
{
    public function index()
    {
        $connexions = CalendarConnection::where('user_id', Auth::id())->get()->keyBy('provider');

        return view('tenant.calendrier.connecter', compact('connexions'));
    }

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'microsoft'], true), 404);

        $driver = Socialite::driver($provider)->stateless();

        if ($provider === 'google') {
            $driver->scopes(['https://www.googleapis.com/auth/calendar.readonly'])
                ->with(['access_type' => 'offline', 'prompt' => 'consent']);
        }

        if ($provider === 'microsoft') {
            $driver->scopes([
                'openid', 'profile', 'email', 'offline_access',
                'https://graph.microsoft.com/Calendars.Read',
            ]);
        }

        return $driver->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, ['google', 'microsoft'], true), 404);

        $utilisateurSocial = Socialite::driver($provider)->stateless()->user();

        CalendarConnection::updateOrCreate(
            ['user_id' => Auth::id(), 'provider' => $provider],
            [
                'access_token' => $utilisateurSocial->token,
                'refresh_token' => $utilisateurSocial->refreshToken ?? null,
                'token_expires_at' => $utilisateurSocial->expiresIn
                    ? now()->addSeconds($utilisateurSocial->expiresIn)
                    : null,
                'provider_email' => $utilisateurSocial->getEmail(),
                'calendar_id' => null,
            ]
        );

        return redirect()->route('tenant.calendrier.index')
            ->with('status', ucfirst($provider).' connecté avec succès.');
    }

    public function destroy(CalendarConnection $connection): RedirectResponse
    {
        abort_unless($connection->user_id === Auth::id(), 403);

        $connection->delete();

        return redirect()->route('tenant.calendrier.index')
            ->with('status', 'Calendrier déconnecté.');
    }
}
