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
            ->line(new \Illuminate\Support\HtmlString('<div style="text-align: center; margin-bottom: 20px;"><img src="' . asset('images/logo/logo-indosehat-horizontal.png') . '" alt="Klinik Indosehat 2003" style="max-height: 48px; width: auto; object-fit: contain;" /></div>'))
            ->line('Akun Anda telah didaftarkan pada Sistem Informasi Medical Check-Up Klinik Indosehat 2003.')
            ->line('**Detail Kredensial Pengguna:**')
            ->line('• Nama Pengguna: **' . ($notifiable->username ?? '-') . '**')
            ->line('• Alamat Surel: **' . $notifiable->email . '**')
            ->line('• Tingkat Wewenang: **' . $peranLabel . '**')
            ->line('**Langkah-langkah Aktivasi Akun:**')
            ->line('1. Klik tombol **Aktivasi Akun & Atur Kata Sandi** di bawah.')
            ->line('2. Buat kata sandi baru Anda yang kuat dan aman.')
            ->line('3. Masuk ke portal administrasi dan mulai kelola data MCU.')
            ->action('Aktivasi Akun & Atur Kata Sandi', $resetUrl)
            ->line('Demi standar keamanan rekam medis, tautan aktivasi ini berlaku selama **24 jam (1.440 menit)**.')
            ->line('Jika Anda tidak merasa didaftarkan oleh administrator klinik, abaikan surel ini.')
            ->salutation("Salam hangat,\nKlinik Indosehat 2003");
    }
}
