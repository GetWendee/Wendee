<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ModificationEffectueeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Client $client, public string $module)
    {
    }

    public function build(): self
    {
        return $this->subject('Modification apportée à votre dossier')
            ->view('emails.modification-effectuee');
    }
}
