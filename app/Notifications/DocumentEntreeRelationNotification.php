<?php

namespace App\Notifications;

use App\Models\Client;
use App\Models\CabinetProfile;
use App\Services\DerService;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class DocumentEntreeRelationNotification extends Notification
{
    public function __construct(protected Client $client)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $cabinet = CabinetProfile::query()->first();
        $cabinetName = $cabinet?->nom_commercial ?: ($cabinet?->raison_sociale ?: 'Wendee');
        $logoUrl = $cabinet?->logo ? asset('storage/'.$cabinet->logo) : asset('images/logo-wendee.png');

        $pdf = DerService::genererPdf($this->client);
        $filename = 'Document-entree-en-relation-'
            .Str::slug($this->client->nom)
            .'-'.now()->format('Y-m-d').'.pdf';

        return (new MailMessage)
            ->subject("Document d'entrée en relation : {$cabinetName}")
            ->view('emails.document-entree-relation', [
                'client' => $this->client,
                'cabinet' => $cabinet,
                'cabinetName' => $cabinetName,
                'logoUrl' => $logoUrl,
            ])
            ->attachData($pdf->output(), $filename, ['mime' => 'application/pdf']);
    }
}
