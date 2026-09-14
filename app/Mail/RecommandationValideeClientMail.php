<?php

namespace App\Mail;

use App\Models\CabinetProfile;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Email envoyé au client juste après qu'il ait validé (code + case
 * "j'accepte") sa recommandation patrimoniale : confirme la validation et
 * joint le PDF signé (nom du client en signature manuscrite, voir
 * tenant.clients.pdf.recommandation-patrimoniale).
 */
class RecommandationValideeClientMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public ?CabinetProfile $cabinet,
        public string $pdfContent,
        public string $filename,
    ) {
    }

    public function build(): self
    {
        $nomCabinet = $this->cabinet?->nom_commercial ?: 'Wendee';

        return $this->subject('Votre recommandation patrimoniale validée - ' . $nomCabinet)
            ->view('emails.recommandation-patrimoniale-validee')
            ->with([
                'client' => $this->client,
                'nomCabinet' => $nomCabinet,
            ])
            ->attachData($this->pdfContent, $this->filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
