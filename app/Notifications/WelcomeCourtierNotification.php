<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeCourtierNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $token,
        protected string $domain,
        protected string $cabinetName,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = 'https://'.$this->domain.'/reset-password/'
            .$this->token
            .'?email='.urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Bienvenue chez Wendee : activez votre compte')
            ->view('emails.welcome-courtier', [
                'user' => $notifiable,
                'cabinetName' => $this->cabinetName,
                'domain' => $this->domain,
                'activationUrl' => $url,
            ]);
    }
}
