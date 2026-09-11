<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeReactivationNotification extends Notification
{
    public function __construct(
        protected Client $client,
        protected \App\Models\User $demandeur,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'demande_reactivation',
            'client_id' => $this->client->id,
            'client_nom' => $this->client->nomAffichage(),
            'demandeur_nom' => $this->demandeur->name,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Demande de réactivation : '.$this->client->nomAffichage())
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->demandeur->name.' souhaite réactiver le dossier archivé de '.$this->client->nomAffichage().'.')
            ->action('Traiter la demande', route('tenant.comptes-clotures.index'))
            ->line('Seul le courtier peut réactiver un dossier archivé.');
    }
}
