<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Password;

class UndanganPenggunaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('filament.admin.auth.password-reset.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Undangan Bergabung - Sistem MCU Klinik Indosehat 2003')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Anda telah didaftarkan sebagai pengguna pada Sistem Informasi Medical Check-Up Klinik Indosehat 2003 dengan peran: **' . strtoupper($notifiable->peran ?? 'STAFF') . '**.')
            ->line('Silakan klik tombol di bawah ini untuk mengaktifkan akun dan mengatur kata sandi Anda:')
            ->action('Aktivasi Akun & Atur Kata Sandi', $resetUrl)
            ->line('Tautan aktivasi ini akan kedaluwarsa dalam 60 menit.')
            ->line('Jika Anda tidak merasa didaftarkan, silakan abaikan surel ini.')
            ->salutation('Salam hormat,' . "\n" . 'Tim IT Klinik Indosehat 2003');
    }
}
