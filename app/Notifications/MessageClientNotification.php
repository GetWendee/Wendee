<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageClientNotification extends Notification
{
    public function __construct(
        protected Client $client,
        protected string $message,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'message_client',
            'client_id' => $this->client->id,
            'client_nom' => $this->client->nomAffichage(),
            'message' => $this->message,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Message de '.$this->client->nomAffichage())
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->client->nomAffichage().' vous a envoyé un message depuis son espace client :')
            ->line('« '.$this->message.' »')
            ->action('Voir la fiche client', route('tenant.clients.show', $this->client));
    }
}
