<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Notification;

class DemandeRendezVousNotification extends Notification
{
    public function __construct(
        protected Client $client,
        protected bool $urgent,
        protected string $sujet,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'demande_rendez_vous',
            'client_id' => $this->client->id,
            'client_nom' => $this->client->nomAffichage(),
            'urgent' => $this->urgent,
            'sujet' => $this->sujet,
        ];
    }
}
