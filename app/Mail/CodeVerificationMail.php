<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CodeVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Client $client, public string $module, public string $code)
    {
    }

    public function build(): self
    {
        return $this->subject('Votre code de vérification')
            ->view('emails.code-verification');
    }
}
