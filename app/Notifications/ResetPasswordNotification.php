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
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun Sistem Informasi MCU Anda.')
            ->line('**Detail Akun Terdaftar:**')
            ->line('• Nama Pengguna: **' . ($notifiable->username ?? '-') . '**')
            ->line('• Alamat Surel: **' . $notifiable->email . '**')
            ->line('Silakan klik tombol di bawah untuk memasukkan kata sandi baru:')
            ->action('Atur Ulang Kata Sandi', $resetUrl)
            ->line('Demi standar keamanan rekam medis, tautan verifikasi ini hanya berlaku selama **60 menit**.')
            ->line('Jika Anda tidak merasa mengajukan permintaan ini, amankan akun Anda segera atau hubungi administrator klinik.')
            ->salutation("Salam hangat,\nKlinik Indosehat 2003");
    }
}
