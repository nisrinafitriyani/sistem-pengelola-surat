<?php

namespace App\Filament\Resources\Approvals\Pages;

use App\Filament\Resources\Approvals\ApprovalResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListApprovals extends ListRecords
{
    protected static string $resource = ApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_report')
                ->label('Cetak PDF Report')
                ->icon('heroicon-o-printer')
                ->color('info')
                ->url(function () {
                    $dateFilter = $this->getTableFilterState('date') ?? [];
                    $typeFilter = $this->getTableFilterState('type') ?? [];
                    $statusFilter = $this->getTableFilterState('status') ?? [];

                    return route('report.persetujuan.pdf', [
                        'from' => $dateFilter['date_from'] ?? null,
                        'to' => $dateFilter['date_to'] ?? null,
                        'type' => $typeFilter['value'] ?? null,
                        'status' => $statusFilter['value'] ?? null,
                        'search' => $this->getTableSearch(),
                    ]);
                })
                ->openUrlInNewTab(),
            // No CreateAction — approval dibuat otomatis
        ];
    }
}
