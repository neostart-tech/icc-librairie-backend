<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VenteComptoirMail extends Mailable
{
    use Queueable, SerializesModels;

    public $commande;

    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
    }

    public function build()
    {
        return $this->subject('Reçu de votre achat - ICC Librairie - #' . $this->commande->reference)
                    ->view('emails.vente-comptoir');
    }
}
