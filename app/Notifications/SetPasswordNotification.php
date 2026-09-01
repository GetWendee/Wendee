<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SetPasswordNotification extends Notification
{
    public function __construct(public string $token)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $url = url(route('tenant.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $cabinetName = 'Wendee';

        if (function_exists('tenant') && tenant()) {
            try {
                $cabinet = \App\Models\CabinetProfile::query()->first();
                $cabinetName = $cabinet?->nom_commercial ?: 'Wendee';
            } catch (\Throwable $e) {
                $cabinetName = 'Wendee';
            }
        }

        return (new MailMessage)
            ->subject("Bienvenue sur {$cabinetName} : définissez votre mot de passe")
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line("Un compte vient d'être créé pour vous sur {$cabinetName}.")
            ->line('Cliquez sur le bouton ci-dessous pour définir votre mot de passe et activer votre compte.')
            ->action('Définir mon mot de passe', $url)
            ->line('Ce lien expire dans 60 minutes. Si vous n\'êtes pas à l\'origine de cette création de compte, vous pouvez ignorer cet email.')
            ->salutation("Cordialement,\nL'équipe {$cabinetName}");
    }
}
