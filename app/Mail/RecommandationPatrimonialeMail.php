<?php

namespace App\Mail;

use App\Models\CabinetProfile;
use App\Models\Client;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecommandationPatrimonialeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Client $client,
        public ?CabinetProfile $cabinet,
        public ?User $conseiller,
        public string $pdfContent,
        public string $filename,
    ) {
    }

    public function build(): self
    {
        $nomCabinet = $this->cabinet?->nom_commercial ?: 'Wendee';

        return $this->subject('Votre recommandation patrimoniale - ' . $nomCabinet)
            ->view('emails.recommandation-patrimoniale')
            ->with([
                'client' => $this->client,
                'cabinet' => $this->cabinet,
                'conseiller' => $this->conseiller,
                'nomCabinet' => $nomCabinet,
            ])
            ->attachData($this->pdfContent, $this->filename, [
                'mime' => 'application/pdf',
            ]);
    }
}
