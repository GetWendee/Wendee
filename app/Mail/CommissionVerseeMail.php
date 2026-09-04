<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommissionVerseeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $apporteur,
        public Collection $commissions,
    ) {
    }

    public function build(): self
    {
        return $this->subject('Virement de vos commissions')
            ->view('emails.commission-versee')
            ->with([
                'apporteur' => $this->apporteur,
                'commissions' => $this->commissions,
                'total' => $this->commissions->sum('montant_commission'),
            ]);
    }
}
