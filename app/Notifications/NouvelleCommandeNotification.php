<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleCommandeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $commande)
    {
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle Commande Reçue - ICC Librairie')
            ->view('emails.new-order-admin', [
                'commande' => $this->commande,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'type' => 'commande',
            'titre' => 'Nouvelle commande',
            'message' => "Nouvelle commande {$this->commande->reference} créée",
            'commande_id' => $this->commande->id,
            'montant' => $this->commande->prix_total,
        ];
    }
}
