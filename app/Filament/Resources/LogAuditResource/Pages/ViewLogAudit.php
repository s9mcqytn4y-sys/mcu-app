<?php

declare(strict_types=1);

namespace App\Filament\Resources\LogAuditResource\Pages;

use App\Filament\Resources\LogAuditResource;
use Filament\Resources\Pages\ViewRecord;

class ViewLogAudit extends ViewRecord
{
    protected static string $resource = LogAuditResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
