<?php

namespace App\Mail;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentRefusedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $commande;
    public $motif;

    public function __construct(Commande $commande, $motif)
    {
        $this->commande = $commande;
        $this->motif = $motif;
    }

    public function build()
    {
        return $this->subject('Paiement refusé - Commande #' . $this->commande->reference)
                    ->view('emails.payment-refused');
    }
}
