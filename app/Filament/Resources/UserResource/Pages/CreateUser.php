<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Notifications\UndanganPenggunaNotification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
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
            try {
                $token = Password::broker()->createToken($record);
                $record->notify(new UndanganPenggunaNotification($token));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim surel aktivasi saat pembuatan pengguna baru: ' . $e->getMessage(), [
                    'email' => $record->email,
                    'trace' => $e->getTraceAsString(),
                ]);
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
            ? 'Pengguna baru berhasil dibuat dan undangan aktivasi telah dimasukkan ke antrean pengiriman email untuk ' . $record->email
            : 'Pengguna baru berhasil ditambahkan';
    }
}
