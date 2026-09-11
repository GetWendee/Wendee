<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArchivageImminentNotification extends Notification
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
            'type' => 'archivage_imminent',
            'client_id' => $this->client->id,
            'client_nom' => $this->client->nomAffichage(),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Archivage dans 3 jours : '.$this->client->nomAffichage())
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line('Le dossier de '.$this->client->nomAffichage().', clôturé depuis près de 6 mois, sera archivé dans 3 jours.')
            ->line('Une fois archivé, il faudra une réactivation par le courtier (ou une demande de votre part) pour y accéder à nouveau, pendant 5 ans.')
            ->action('Voir Comptes clôturés', route('tenant.comptes-clotures.index'))
            ->line('Réactivez-le avant l\'échéance si ce n\'est pas voulu.');
    }
}
