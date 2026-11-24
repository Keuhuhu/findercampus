<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyCampusEmail extends Notification
{
    public function __construct(
        private readonly string $recipientName,
        private readonly string $verificationUrl,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi email kampus FinderCampus')
            ->greeting('Halo '.$this->recipientName)
            ->line('Klik tombol berikut untuk memverifikasi email kampus dan mendapatkan lencana Email Unsri Terverifikasi.')
            ->action('Verifikasi Email Kampus', $this->verificationUrl)
            ->line('Tautan berlaku selama '.config('auth.verification.expire', 60).' menit.')
            ->line('Jika Anda tidak meminta verifikasi ini, abaikan email ini.');
    }
}
