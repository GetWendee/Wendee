<?php

namespace App\Services;

use App\Models\DeviceLoginChallenge;
use App\Models\KnownDevice;
use App\Models\User;
use App\Notifications\NewDeviceLoginNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class DeviceLoginChallengeService
{
    public const COOKIE_NAME = 'wd_device';

    public static function creerChallenge(User $user, Request $request): DeviceLoginChallenge
    {
        $challenge = DeviceLoginChallenge::create([
            'user_id' => $user->id,
            'token' => Str::random(48),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'status' => 'pending',
            'expires_at' => now()->addMinutes(15),
        ]);

        $user->notify(new NewDeviceLoginNotification($challenge));

        return $challenge;
    }

    /**
     * Marque le challenge comme expiré s'il a dépassé son délai. À appeler
     * avant toute lecture de son statut.
     */
    public static function actualiser(DeviceLoginChallenge $challenge): DeviceLoginChallenge
    {
        if ($challenge->status === 'pending' && $challenge->estExpiree()) {
            $challenge->update(['status' => 'expired']);
        }

        return $challenge;
    }

    public static function confirmer(DeviceLoginChallenge $challenge): bool
    {
        $challenge = self::actualiser($challenge);

        if ($challenge->status !== 'pending') {
            return false;
        }

        $challenge->update(['status' => 'confirmed']);

        return true;
    }

    public static function refuser(DeviceLoginChallenge $challenge): bool
    {
        $challenge = self::actualiser($challenge);

        if ($challenge->status !== 'pending') {
            return false;
        }

        $challenge->update(['status' => 'denied']);

        Password::broker()->sendResetLink(['email' => $challenge->user->email]);

        return true;
    }

    public static function appareilConnu(User $user, ?string $cookieValue): bool
    {
        if (! $cookieValue) {
            return false;
        }

        $known = KnownDevice::where('user_id', $user->id)
            ->where('device_token_hash', hash('sha256', $cookieValue))
            ->first();

        if (! $known) {
            return false;
        }

        $known->update(['last_seen_at' => now()]);

        return true;
    }

    /**
     * Enregistre l'appareil courant comme connu pour cet utilisateur et
     * renvoie le token brut à placer dans le cookie du navigateur.
     */
    public static function enregistrerAppareilConnu(User $user, Request $request): string
    {
        $token = Str::random(64);

        KnownDevice::create([
            'user_id' => $user->id,
            'device_token_hash' => hash('sha256', $token),
            'label' => self::libelleAppareil((string) $request->userAgent()),
            'ip_address' => $request->ip(),
            'last_seen_at' => now(),
        ]);

        return $token;
    }

    public static function libelleAppareil(string $userAgent): string
    {
        $navigateur = match (true) {
            str_contains($userAgent, 'Edg/') => 'Edge',
            str_contains($userAgent, 'Chrome/') => 'Chrome',
            str_contains($userAgent, 'Firefox/') => 'Firefox',
            str_contains($userAgent, 'Safari/') => 'Safari',
            default => 'Navigateur inconnu',
        };

        $systeme = match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Mac OS') => 'macOS',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone'), str_contains($userAgent, 'iPad') => 'iOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'appareil inconnu',
        };

        return "{$navigateur} sur {$systeme}";
    }
}
