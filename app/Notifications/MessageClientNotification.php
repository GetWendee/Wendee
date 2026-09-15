<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class MessageClientNotification extends Notification
{
    public function __construct(
        protected Client $client,
        protected string $message,
        protected ?string $pieceJointePath = null,
        protected ?string $pieceJointeNom = null,
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
            'piece_jointe_nom' => $this->pieceJointeNom,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Message de '.$this->client->nomAffichage())
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->client->nomAffichage().' vous a envoyé un message depuis son espace client :')
            ->line('« '.$this->message.' »');

        if ($this->pieceJointePath) {
            $mail->attach(Storage::disk('local')->path($this->pieceJointePath), [
                'as' => $this->pieceJointeNom ?? basename($this->pieceJointePath),
            ]);
        }

        return $mail->action('Voir la fiche client', route('tenant.clients.show', $this->client));
    }
}
