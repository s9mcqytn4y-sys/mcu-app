<?php

declare(strict_types=1);

namespace App\Filament\Resources\PemeriksaanMcuResource\Pages;

use App\Filament\Resources\PemeriksaanMcuResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPemeriksaanMcus extends ListRecords
{
    protected static string $resource = PemeriksaanMcuResource::class;

    public function mount(): void
    {
        parent::mount();
        session(['mcu_badge_viewed' => true]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Catat Pemeriksaan MCU'),
        ];
    }
}
