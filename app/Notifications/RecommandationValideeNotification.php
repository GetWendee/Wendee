<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RecommandationValideeNotification extends Notification
{
    public function __construct(
        protected Client $client,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'recommandation_validee',
            'client_id' => $this->client->id,
            'client_nom' => $this->client->nomAffichage(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Recommandation patrimoniale validée par ' . $this->client->nomAffichage())
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->client->nomAffichage() . ' vient de valider sa recommandation patrimoniale depuis son espace client.')
            ->action('Voir la fiche client', route('tenant.clients.show', $this->client))
            ->line('Vous pouvez désormais échanger sur le plan d\'action.');
    }
}
