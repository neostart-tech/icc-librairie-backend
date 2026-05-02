<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentDeclaredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $commande;

    public function __construct($commande)
    {
        $this->commande = $commande;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        // Admin notification
        if ($notifiable->role && in_array($notifiable->role->role, ['admin', 'superadmin'])) {
            return (new MailMessage)
                ->subject('Déclaration de Paiement Reçue - Commande #' . $this->commande->reference)
                ->view('emails.new-payment-admin', [
                    'commande' => $this->commande,
                ]);
        }

        // User notification
        return (new MailMessage)
            ->subject('Paiement Déclaré - Commande #' . $this->commande->reference)
            ->view('emails.payment-declared', [
                'commande' => $this->commande,
            ]);
    }

    public function toDatabase($notifiable)
    {
        $isAdmin = ($notifiable->role && in_array($notifiable->role->role, ['admin', 'superadmin']));

        return [
            'title' => $isAdmin ? 'Nouveau Paiement Déclaré' : 'Paiement Déclaré',
            'message' => $isAdmin 
                ? "Un client a déclaré le paiement pour la commande #{$this->commande->reference}."
                : "Votre déclaration de paiement pour la commande #{$this->commande->reference} a été enregistrée.",
            'commande_id' => $this->commande->id,
            'type' => 'paiement_declare'
        ];
    }
}
