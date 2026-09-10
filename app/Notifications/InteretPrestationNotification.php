<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InteretPrestationNotification extends Notification
{
    public function __construct(
        protected Client $client,
        protected string $titre,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'interet_prestation',
            'client_id' => $this->client->id,
            'client_nom' => $this->client->nomAffichage(),
            'titre' => $this->titre,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Un client souhaite échanger sur : '.$this->titre)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->client->nomAffichage().' a manifesté son intérêt pour « '.$this->titre.' » depuis son espace client.')
            ->line('Il souhaite en discuter avec vous.')
            ->action('Voir la fiche client', route('tenant.clients.show', $this->client))
            ->line('Pensez à le recontacter dès que possible.');
    }
}
