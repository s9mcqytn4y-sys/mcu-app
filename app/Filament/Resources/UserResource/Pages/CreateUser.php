<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Notifications\UndanganPenggunaNotification;
use Filament\Facades\Filament;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['password'])) {
            $data['password'] = Hash::make(Str::random(32));
            $data['token_undangan'] = Str::random(60);
            $data['undangan_dikirim_pada'] = now();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var User $record */
        $record = $this->record;

        if (! empty($record->token_undangan)) {
            $token = null;
            try {
                DB::table('password_reset_tokens')
                    ->where('email', $record->email)
                    ->delete();

                $token = Password::broker()->createToken($record);
                $record->notify(new UndanganPenggunaNotification($token));
            } catch (\Throwable $e) {
                Log::error('Gagal mengirim surel aktivasi saat pembuatan pengguna baru: '.$e->getMessage(), [
                    'email' => $record->email,
                    'trace' => $e->getTraceAsString(),
                ]);

                $token = $token ?? Password::broker()->createToken($record);
                $tautanAktivasi = Filament::getResetPasswordUrl($token, $record);

                Notification::make()
                    ->title('Pengiriman Surel Aktivasi Terkendala')
                    ->body('Pengguna berhasil dibuat, namun surel undangan tidak dapat dikirim otomatis (kuota/layanan SMTP terkendala). Tautan aktivasi dapat disalin langsung.')
                    ->warning()
                    ->persistent()
                    ->actions([
                        Action::make('buka_tautan_darurat')
                            ->label('Buka Tautan Aktivasi')
                            ->url($tautanAktivasi)
                            ->openUrlInNewTab(),
                    ])
                    ->send();
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        /** @var User $record */
        $record = $this->record;

        return ! empty($record->token_undangan)
            ? 'Pengguna baru berhasil dibuat dan undangan aktivasi telah dimasukkan ke antrean pengiriman email untuk '.$record->email
            : 'Pengguna baru berhasil ditambahkan';
    }
}
