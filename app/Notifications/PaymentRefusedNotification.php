<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRefusedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $commande;
    public $motif;

    public function __construct($commande, $motif)
    {
        $this->commande = $commande;
        $this->motif = $motif;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Paiement Refusé - Commande #' . $this->commande->reference)
            ->view('emails.payment-refused', [
                'commande' => $this->commande,
                'motif' => $this->motif
            ]);
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Paiement Refusé',
            'message' => "Le paiement pour votre commande #{$this->commande->reference} a été refusé. Motif : {$this->motif}",
            'commande_id' => $this->commande->id,
            'type' => 'paiement_refuse'
        ];
    }
}
