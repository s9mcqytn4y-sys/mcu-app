<?php

declare(strict_types=1);

namespace App\Filament\Resources\PasienResource\Pages;

use App\Filament\Resources\PasienResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPasien extends EditRecord
{
    protected static string $resource = PasienResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->modalHeading('Konfirmasi Hapus Data Pasien')
                ->modalDescription('Apakah Anda yakin ingin menghapus data pasien ini? Seluruh data pemeriksaan terkait juga akan terpengaruh.')
                ->modalSubmitActionLabel('Ya, Hapus Pasien')
                ->modalCancelActionLabel('Batal')
                ->visible(fn (): bool => ($user = auth()->user()) instanceof \App\Models\User && $user->apakahAdministrator()),
        ];
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Data pasien berhasil diperbarui';
    }
}
