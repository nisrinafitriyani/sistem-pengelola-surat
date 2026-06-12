<?php

namespace App\Filament\Widgets;

use App\Models\Quotation;
use App\Models\Approval;
use App\Models\Invoice;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $dateFrom = $this->pageFilters['date_from'] ?? null;
        $dateTo = $this->pageFilters['date_to'] ?? null;

        $queryDate = function ($query) use ($dateFrom, $dateTo) {
            return $query->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
                         ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo));
        };
        
        $queryUpdatedAt = function ($query) use ($dateFrom, $dateTo) {
            return $query->when($dateFrom, fn($q) => $q->whereDate('updated_at', '>=', $dateFrom))
                         ->when($dateTo, fn($q) => $q->whereDate('updated_at', '<=', $dateTo));
        };

        $periodLabel = 'Periode Ini';
        if ($dateFrom && $dateTo) {
            $periodLabel = \Carbon\Carbon::parse($dateFrom)->translatedFormat('d M Y') . ' - ' . \Carbon\Carbon::parse($dateTo)->translatedFormat('d M Y');
        }

        // Total penawaran bulan yang dipilih
        $totalPenawaran = Quotation::where(function($q) use ($queryDate) { return $queryDate($q); })->count();

        // Menunggu persetujuan (approve tapi pending)
        $menunggu = Approval::where('status', 'pending')
            ->whereHas('quotation', fn($q) => $q->where('status', 'approve')->whereNull('deleted_at'))
            ->count(); // Proyek berjalan biarkan tanpa filter date (karena ini current status)

        // Proyek selesai bulan yang dipilih
        $selesai = Approval::where('status', 'completed')
            ->whereHas('quotation', fn($q) => $q->whereNull('deleted_at'))
            ->where(function($q) use ($queryUpdatedAt) { return $queryUpdatedAt($q); })
            ->count();

        // Total nilai invoice bulan yang dipilih
        $totalInvoice = Invoice::where(function($q) use ($queryDate) { return $queryDate($q); })
            ->sum('contract_sum');

        return [
            Stat::make('Penawaran Dibuat', $totalPenawaran)
                ->description('Total surat penawaran')
                ->icon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Proyek Berjalan', $menunggu)
                ->description('Menunggu penyelesaian (Saat ini)')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Proyek Selesai', $selesai)
                ->description('Selesai pada periode ini')
                ->icon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('Nilai Invoice', 'Rp ' . number_format($totalInvoice, 0, ',', '.'))
                ->description('Total invoice periode ini')
                ->icon('heroicon-o-banknotes')
                ->color('info'),
        ];
    }

    /**
     * Get last 6 months quotation count for sparkline chart.
     */
    private function getMonthlyQuotationTrend(): array
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = Quotation::whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->count();
        }
        return $data;
    }
}
