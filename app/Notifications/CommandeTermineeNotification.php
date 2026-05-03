<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class CommandeTermineeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $commande;

    public function __construct($commande)
    {
        $this->commande = $commande;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Admin notification
        if ($notifiable->role && in_array($notifiable->role->role, ['admin', 'superadmin'])) {
            return (new MailMessage)
                ->subject('Paiement Reçu (Automatique) - Commande #' . $this->commande->reference)
                ->view('emails.order-treated-admin', [
                    'commande' => $this->commande,
                ]);
        }

        // User notification
        return (new MailMessage)
            ->subject('Confirmation de votre commande - ICC Librairie')
            ->view('emails.order-confirmed-user', [
                'commande' => $this->commande,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase($notifiable)
    {
        $isAdmin = ($notifiable->role && in_array($notifiable->role->role, ['admin', 'superadmin']));

        return [
            'title' => $isAdmin ? 'Paiement Reçu' : 'Commande effectuée',
            'message' => $isAdmin 
                ? "Un paiement automatique a été reçu pour la commande #{$this->commande->reference}."
                : "Votre commande #{$this->commande->reference} a été effectuée avec succès. Nous procédons au traitement.",
            'commande_id' => $this->commande->id,
            'type' => 'commande_terminee'
        ];
    }
}
