<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
        $resetUrl = \Filament\Facades\Filament::getResetPasswordUrl($this->token, $notifiable);

        $peranLabel = match ($notifiable->peran ?? 'staf') {
            'administrator' => 'Administrator Sistem (Akses Penuh)',
            default => 'Staf Operasional (MCU & Pasien)',
        };

        return (new MailMessage)
            ->subject('Undangan Aktivasi Akun - Klinik Indosehat 2003')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Akun Anda telah didaftarkan pada Sistem Informasi Medical Check-Up Klinik Indosehat 2003.')
            ->line('**Detail Kredensial Pengguna:**')
            ->line('• Nama Pengguna: **' . ($notifiable->username ?? '-') . '**')
            ->line('• Alamat Surel: **' . $notifiable->email . '**')
            ->line('• Tingkat Wewenang: **' . $peranLabel . '**')
            ->line('Silakan klik tombol di bawah untuk mengaktifkan akun dan mengatur kata sandi Anda:')
            ->action('Aktivasi Akun & Atur Kata Sandi', $resetUrl)
            ->line('Demi keamanan kredensial medis, tautan ini hanya berlaku selama **60 menit**.')
            ->line('Jika Anda tidak merasa didaftarkan oleh administrator klinik, abaikan surel ini.')
            ->salutation("Salam hangat,\nKlinik Indosehat 2003");
    }
}
