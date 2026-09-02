<?php

namespace App\Notifications;

use App\Models\CabinetProfile;
use App\Models\DeviceLoginChallenge;
use App\Services\DeviceLoginChallengeService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewDeviceLoginNotification extends Notification
{
    public function __construct(protected DeviceLoginChallenge $challenge)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $cabinet = function_exists('tenant') && tenant() ? CabinetProfile::query()->first() : null;
        $cabinetName = $cabinet?->nom_commercial ?: ($cabinet?->raison_sociale ?: 'Wendee');

        $confirmUrl = URL::temporarySignedRoute(
            'tenant.device-login.confirm',
            $this->challenge->expires_at,
            ['challenge' => $this->challenge->token]
        );

        $denyUrl = URL::temporarySignedRoute(
            'tenant.device-login.deny',
            $this->challenge->expires_at,
            ['challenge' => $this->challenge->token]
        );

        return (new MailMessage)
            ->subject("Nouvelle connexion à votre compte {$cabinetName}")
            ->view('emails.new-device-login', [
                'cabinetName' => $cabinetName,
                'appareil' => DeviceLoginChallengeService::libelleAppareil($this->challenge->user_agent ?? ''),
                'ip' => $this->challenge->ip_address,
                'heure' => $this->challenge->created_at->translatedFormat('d F Y à H:i'),
                'confirmUrl' => $confirmUrl,
                'denyUrl' => $denyUrl,
            ]);
    }
}
