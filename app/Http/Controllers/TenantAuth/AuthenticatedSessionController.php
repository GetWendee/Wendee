<?php

namespace App\Http\Controllers\TenantAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\DeviceLoginChallengeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('tenant.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = $request->authenticateOnce();

        $appareilConnu = DeviceLoginChallengeService::appareilConnu(
            $user,
            $request->cookie(DeviceLoginChallengeService::COOKIE_NAME)
        );

        if ($appareilConnu) {
            Auth::login($user, $request->boolean('remember'));

            $request->session()->regenerate();

            return redirect()->intended(
                route('tenant.dashboard', absolute: false)
            );
        }

        // Appareil inconnu : on ne connecte pas tout de suite, on attend la
        // confirmation par mail ou par une autre session déjà connectée.
        $challenge = DeviceLoginChallengeService::creerChallenge($user, $request);

        $request->session()->put('device_login_challenge', $challenge->token);

        return redirect()->route('tenant.device-login.pending');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('tenant.login');
    }
}
