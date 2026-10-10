<?php

declare(strict_types=1);

namespace App\Filament\Resources\PemeriksaanMcuResource\Pages;

use App\Filament\Resources\PemeriksaanMcuResource;
use App\Models\LogAudit;
use App\Models\PemeriksaanMcu;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPemeriksaanMcu extends EditRecord
{
    protected static string $resource = PemeriksaanMcuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->isAdministrator() ?? false),
        ];
    }

    protected function afterSave(): void
    {
        LogAudit::rekam(
            tindakan: 'ubah_mcu',
            tipeModel: PemeriksaanMcu::class,
            idModel: $this->record->id,
            keterangan: 'Pembaruan data rekam MCU nomor ' . $this->record->nomor_mcu,
        );
    }
}
