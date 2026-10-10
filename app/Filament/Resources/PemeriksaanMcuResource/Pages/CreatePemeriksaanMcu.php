<?php

declare(strict_types=1);

namespace App\Filament\Resources\PemeriksaanMcuResource\Pages;

use App\Filament\Resources\PemeriksaanMcuResource;
use App\Models\LogAudit;
use App\Models\PemeriksaanMcu;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePemeriksaanMcu extends CreateRecord
{
    protected static string $resource = PemeriksaanMcuResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['token_verifikasi'] = (string) Str::uuid();
        $data['dibuat_oleh'] = \Illuminate\Support\Facades\Auth::id() ?? 1;

        return $data;
    }

    protected function afterCreate(): void
    {
        LogAudit::rekam(
            tindakan: 'buat_mcu',
            tipeModel: PemeriksaanMcu::class,
            idModel: $this->record->id,
            keterangan: 'Penerbitan baru sertifikat MCU nomor ' . $this->record->nomor_mcu,
        );
    }
}
