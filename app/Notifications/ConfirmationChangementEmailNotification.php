<?php

namespace App\Notifications;

use App\Models\DemandeChangementEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConfirmationChangementEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        public DemandeChangementEmail $demande,
        public string $urlValider,
        public string $urlRefuser,
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Confirmation de changement d'adresse e-mail")
            ->greeting('Bonjour ' . $notifiable->name)
            ->line("Une demande de changement d'adresse e-mail a été faite sur votre compte Wendee.")
            ->line('Nouvelle adresse demandée : ' . $this->demande->nouvel_email)
            ->line("Si vous êtes à l'origine de cette demande, validez-la ci-dessous.")
            ->action('Valider ce changement', $this->urlValider)
            ->line("Si vous n'êtes pas à l'origine de cette demande, votre compte a probablement été compromis. Cliquez sur le lien ci-dessous : votre compte sera immédiatement bloqué et vous serez déconnecté.")
            ->line('[Refuser et bloquer mon compte](' . $this->urlRefuser . ')')
            ->line('Ce lien expire dans 48 heures.');
    }
}
