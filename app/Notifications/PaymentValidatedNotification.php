<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentValidatedNotification extends Notification implements ShouldQueue
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
                ->subject('Paiement Validé - Notification Interne #' . $this->commande->reference)
                ->view('emails.payment-validated', [
                    'commande' => $this->commande,
                    'isAdmin' => true
                ]);
        }

        // User notification
        return (new MailMessage)
            ->subject('Paiement Validé - Votre commande #' . $this->commande->reference)
            ->view('emails.payment-validated', [
                'commande' => $this->commande,
                'isAdmin' => false
            ]);
    }

    public function toDatabase($notifiable)
    {
        $isAdmin = ($notifiable->role && in_array($notifiable->role->role, ['admin', 'superadmin']));
        
        return [
            'title' => $isAdmin ? 'Paiement Validé' : 'Paiement Confirmé',
            'message' => $isAdmin 
                ? "Le paiement pour la commande #{$this->commande->reference} a été validé."
                : "Le paiement de votre commande #{$this->commande->reference} a été validé avec succès.",
            'commande_id' => $this->commande->id,
            'type' => 'paiement_valide'
        ];
    }
}
