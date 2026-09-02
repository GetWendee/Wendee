<?php

namespace App\Http\Controllers\TenantAuth;

use App\Http\Controllers\Controller;
use App\Models\DeviceLoginChallenge;
use App\Services\DeviceLoginChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class DeviceLoginController extends Controller
{
    /**
     * Page d'attente affichée sur le nouvel appareil, en attendant la
     * confirmation par mail ou par une autre session déjà connectée.
     */
    public function pending(Request $request): View|RedirectResponse
    {
        $token = $request->session()->get('device_login_challenge');

        $challenge = $token ? DeviceLoginChallenge::where('token', $token)->first() : null;

        if (! $challenge) {
            return redirect()->route('tenant.login');
        }

        return view('tenant.auth.device-login-pending');
    }

    /**
     * Poll côté nouvel appareil : où en est le challenge ?
     */
    public function status(Request $request): JsonResponse
    {
        $token = $request->session()->get('device_login_challenge');

        $challenge = $token ? DeviceLoginChallenge::where('token', $token)->first() : null;

        if (! $challenge) {
            return response()->json(['status' => 'introuvable'], 404);
        }

        $challenge = DeviceLoginChallengeService::actualiser($challenge);

        return response()->json(['status' => $challenge->status]);
    }

    /**
     * Termine la connexion sur le nouvel appareil une fois le challenge
     * confirmé (par mail ou par l'autre session).
     */
    public function complete(Request $request): JsonResponse
    {
        $token = $request->session()->get('device_login_challenge');

        $challenge = $token ? DeviceLoginChallenge::where('token', $token)->first() : null;

        if (! $challenge) {
            return response()->json(['status' => 'introuvable'], 404);
        }

        $challenge = DeviceLoginChallengeService::actualiser($challenge);

        if ($challenge->status !== 'confirmed') {
            return response()->json(['status' => $challenge->status], 409);
        }

        $user = $challenge->user;

        Auth::login($user);
        $request->session()->regenerate();

        $rawToken = DeviceLoginChallengeService::enregistrerAppareilConnu($user, $request);

        Cookie::queue(Cookie::make(
            DeviceLoginChallengeService::COOKIE_NAME,
            $rawToken,
            60 * 24 * 365 * 2 // 2 ans
        ));

        $challenge->update(['status' => 'completed']);
        $request->session()->forget('device_login_challenge');

        return response()->json(['redirect' => route('tenant.dashboard')]);
    }

    /**
     * Poll côté session déjà connectée ailleurs : y a-t-il une connexion à
     * confirmer pour cet utilisateur ?
     */
    public function pendingForMe(Request $request): JsonResponse
    {
        $challenge = DeviceLoginChallenge::query()
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $challenge) {
            return response()->json(['challenge' => null]);
        }

        return response()->json([
            'challenge' => [
                'token' => $challenge->token,
                'appareil' => DeviceLoginChallengeService::libelleAppareil($challenge->user_agent ?? ''),
                'ip' => $challenge->ip_address,
                'heure' => $challenge->created_at->translatedFormat('d/m/Y à H:i'),
            ],
        ]);
    }

    /**
     * Confirme un challenge, via le lien mail signé ou via une session déjà
     * connectée (popup).
     */
    public function confirm(Request $request, DeviceLoginChallenge $challenge): View|JsonResponse
    {
        $this->autoriser($request, $challenge);

        $ok = DeviceLoginChallengeService::confirmer($challenge);

        if ($request->wantsJson()) {
            return response()->json(['ok' => $ok, 'status' => $challenge->fresh()->status]);
        }

        return view('tenant.device-login.reponse', [
            'succes' => $ok,
            'titre' => $ok ? 'Connexion autorisée' : 'Ce lien n\'est plus valable',
            'message' => $ok
                ? 'La connexion a été autorisée. Vous pouvez retourner sur l\'appareil qui attendait la confirmation.'
                : 'Ce lien a déjà été utilisé ou a expiré.',
        ]);
    }

    /**
     * Refuse un challenge, via le lien mail signé ou via une session déjà
     * connectée (popup). Déclenche un mail de réinitialisation du mot de
     * passe.
     */
    public function deny(Request $request, DeviceLoginChallenge $challenge): View|JsonResponse
    {
        $this->autoriser($request, $challenge);

        $ok = DeviceLoginChallengeService::refuser($challenge);

        if ($request->wantsJson()) {
            return response()->json(['ok' => $ok, 'status' => $challenge->fresh()->status]);
        }

        return view('tenant.device-login.reponse', [
            'succes' => $ok,
            'titre' => $ok ? 'Connexion refusée' : 'Ce lien n\'est plus valable',
            'message' => $ok
                ? 'La connexion a été bloquée. Un email vous a été envoyé pour redéfinir votre mot de passe.'
                : 'Ce lien a déjà été utilisé ou a expiré.',
        ]);
    }

    /**
     * Un challenge ne peut être confirmé/refusé que via son lien mail signé,
     * ou par la session de l'utilisateur concerné (popup sur un autre
     * appareil déjà connecté).
     */
    private function autoriser(Request $request, DeviceLoginChallenge $challenge): void
    {
        $viaLienSigne = $request->hasValidSignature();
        $viaSessionAutorisee = $request->user() && $request->user()->id === $challenge->user_id;

        abort_unless($viaLienSigne || $viaSessionAutorisee, 403);
    }
}
