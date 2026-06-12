<?php

namespace App\Filament\Resources\Quotations\Pages;

use App\Filament\Resources\Quotations\QuotationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListQuotations extends ListRecords
{
    protected static string $resource = QuotationResource::class;

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

                    return route('report.penawaran.pdf', [
                        'from' => $dateFilter['date_from'] ?? null,
                        'to' => $dateFilter['date_to'] ?? null,
                        'type' => $typeFilter['value'] ?? null,
                        'status' => $statusFilter['value'] ?? null,
                        'search' => $this->getTableSearch(),
                    ]);
                })
                ->openUrlInNewTab(),
            CreateAction::make(),
        ];
    }
}
