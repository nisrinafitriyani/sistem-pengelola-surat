<?php

namespace App\Filament\Resources\Approvals\Pages;

use App\Filament\Resources\Approvals\ApprovalResource;
use Filament\Resources\Pages\EditRecord;

class EditApproval extends EditRecord
{
    protected static string $resource = ApprovalResource::class;

    // No delete actions — delete only via Penawaran
    protected function getHeaderActions(): array
    {
        return [];
    }
}
