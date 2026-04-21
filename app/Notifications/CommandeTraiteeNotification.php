<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class CommandeTraiteeNotification extends Notification
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
                ->subject('Commande Traitée - Notification Interne')
                ->view('emails.order-treated-admin', [
                    'commande' => $this->commande,
                ]);
        }

        // User notification
        return (new MailMessage)
            ->subject('Votre commande a été servie - ICC Librairie')
            ->view('emails.order-treated', [
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
        return [
            'title' => 'Commande livrée',
            'message' => "Votre commande #{$this->commande->reference} a été livrée avec succès.",
            'commande_id' => $this->commande->id,
        ];
    }
}
