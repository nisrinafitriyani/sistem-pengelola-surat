<?php

namespace App\Filament\Resources\Handovers\Pages;

use App\Filament\Resources\Handovers\HandoverResource;
use Filament\Resources\Pages\EditRecord;

class EditHandover extends EditRecord
{
    protected static string $resource = HandoverResource::class;

    // No delete — delete only via Penawaran
    protected function getHeaderActions(): array
    {
        return [];
    }
}
