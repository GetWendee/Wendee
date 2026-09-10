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
        public string $code,
        public ?string $pdfContent = null,
        public ?string $filename = null,
    ) {
    }

    public function build(): self
    {
        $nomCabinet = $this->cabinet?->nom_commercial ?: 'Wendee';

        $mail = $this->subject('Votre recommandation patrimoniale - ' . $nomCabinet)
            ->view('emails.recommandation-patrimoniale')
            ->with([
                'client' => $this->client,
                'cabinet' => $this->cabinet,
                'conseiller' => $this->conseiller,
                'nomCabinet' => $nomCabinet,
                'code' => $this->code,
                'pdfJoint' => $this->pdfContent !== null,
            ]);

        if ($this->pdfContent !== null && $this->filename !== null) {
            $mail->attachData($this->pdfContent, $this->filename, [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
