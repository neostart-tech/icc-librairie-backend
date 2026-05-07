<?php

namespace App\Mail;

use App\Models\Devis;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DevisAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public Devis $devis;

    public function __construct(Devis $devis)
    {
        $this->devis = $devis;
    }

    public function build()
    {
        return $this->subject('Nouvelle demande de devis — ICC Librairie')
                    ->view('emails.devis-admin');
    }
}
