<?php

declare(strict_types=1);

namespace App\Filament\Resources\PemeriksaanMcuResource\Pages;

use App\Filament\Resources\PemeriksaanMcuResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePemeriksaanMcu extends CreateRecord
{
    protected static string $resource = PemeriksaanMcuResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['token_verifikasi'] = (string) Str::uuid();
        $data['dibuat_oleh'] = auth()->id() ?? 1;

        return $data;
    }
}
