<?php

namespace App\Mail;

use App\Models\PromptIa;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PromptIaCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PromptIa $prompt, public User $auteur, public string $code)
    {
    }

    public function build(): self
    {
        return $this->subject('Code de confirmation — Configuration IA')
            ->view('emails.prompt-ia-code');
    }
}
