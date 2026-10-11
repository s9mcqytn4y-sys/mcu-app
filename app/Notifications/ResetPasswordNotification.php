<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
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

        return (new MailMessage)
            ->subject('Permintaan Pemulihan Kata Sandi - Klinik Indosehat 2003')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line(new \Illuminate\Support\HtmlString('<div style="text-align: center; margin-bottom: 20px;"><img src="' . asset('images/logo/logo-indosehat-horizontal.png') . '" alt="Klinik Indosehat 2003" style="max-height: 48px; width: auto; object-fit: contain;" /></div>'))
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Sistem Informasi MCU Anda.')
            ->line('**Detail Akun Terdaftar:**')
            ->line('• Nama Pengguna: **' . ($notifiable->username ?? '-') . '**')
            ->line('• Alamat Surel: **' . $notifiable->email . '**')
            ->line('**Langkah-langkah Pemulihan Kata Sandi:**')
            ->line('1. Klik tombol **Atur Ulang Kata Sandi** di bawah.')
            ->line('2. Masukkan kata sandi baru Anda dan konfirmasikan.')
            ->line('3. Masuk kembali ke sistem menggunakan kata sandi baru Anda.')
            ->action('Atur Ulang Kata Sandi', $resetUrl)
            ->line('Demi standar keamanan rekam medis, tautan verifikasi ini berlaku selama **24 jam (1.440 menit)**.')
            ->line('Jika Anda tidak merasa mengajukan permintaan ini, amankan akun Anda segera atau hubungi administrator klinik.')
            ->salutation("Salam hangat,\nKlinik Indosehat 2003");
    }
}
