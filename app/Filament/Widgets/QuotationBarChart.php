<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class QuotationBarChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '320px';

    public function getHeading(): ?string
    {
        $dateFrom = $this->pageFilters['date_from'] ?? null;
        $dateTo = $this->pageFilters['date_to'] ?? null;

        if ($dateFrom && $dateTo) {
            return 'Tren Penawaran: ' . \Carbon\Carbon::parse($dateFrom)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->translatedFormat('d M Y');
        }

        return 'Tren Penawaran (Semua Waktu)';
    }

    protected function getData(): array
    {
        $dateFrom = $this->pageFilters['date_from'] ?? null;
        $dateTo = $this->pageFilters['date_to'] ?? null;

        $poData = [];
        $woData = [];
        $labels = [];

        $start = $dateFrom ? \Carbon\Carbon::parse($dateFrom)->startOfMonth() : now()->subMonths(5)->startOfMonth();
        $end = $dateTo ? \Carbon\Carbon::parse($dateTo)->startOfMonth() : now()->startOfMonth();

        // Limit range to max 12 months to avoid chart crowding
        if ($start->diffInMonths($end) > 12) {
            $start = $end->copy()->subMonths(11);
        }

        $period = \Carbon\CarbonPeriod::create($start, '1 month', $end);

        foreach ($period as $date) {
            $labels[] = $date->translatedFormat('M Y');

            $poData[] = Quotation::where('type', 'po')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->count();

            $woData[] = Quotation::where('type', 'wo')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Purchase Order (PO)',
                    'data' => $poData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Work Order (WO)',
                    'data' => $woData,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.7)',
                    'borderColor' => 'rgb(245, 158, 11)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
