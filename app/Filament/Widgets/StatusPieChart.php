<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatusPieChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '320px';

    public function getHeading(): ?string
    {
        $dateFrom = $this->pageFilters['date_from'] ?? null;
        $dateTo = $this->pageFilters['date_to'] ?? null;
        
        if ($dateFrom && $dateTo) {
            return 'Status Penawaran: ' . \Carbon\Carbon::parse($dateFrom)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->translatedFormat('d M Y');
        }
        
        return 'Status Penawaran';
    }

    protected function getData(): array
    {
        $dateFrom = $this->pageFilters['date_from'] ?? null;
        $dateTo = $this->pageFilters['date_to'] ?? null;

        $queryDate = function ($query) use ($dateFrom, $dateTo) {
            return $query->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                         ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo));
        };

        $draft = Quotation::where('status', 'draft')
            ->where(function($q) use ($queryDate) { return $queryDate($q); })
            ->count();
        $approve = Quotation::where('status', 'approve')
            ->where(function($q) use ($queryDate) { return $queryDate($q); })
            ->count();
        $reject = Quotation::where('status', 'reject')
            ->where(function($q) use ($queryDate) { return $queryDate($q); })
            ->count();

        return [
            'datasets' => [
                [
                    'data' => [$draft, $approve, $reject],
                    'backgroundColor' => [
                        'rgba(156, 163, 175, 0.8)',  // gray for draft
                        'rgba(34, 197, 94, 0.8)',    // green for approve
                        'rgba(239, 68, 68, 0.8)',    // red for reject
                    ],
                    'borderColor' => [
                        'rgb(156, 163, 175)',
                        'rgb(34, 197, 94)',
                        'rgb(239, 68, 68)',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'labels' => ['Draft', 'Diterima', 'Ditolak'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => true,
            'aspectRatio' => 2,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
