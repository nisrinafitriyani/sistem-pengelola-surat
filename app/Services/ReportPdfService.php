<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\Approval;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportPdfService
{
    public function streamPenawaran(?string $from, ?string $to, ?string $type, ?string $status = null, ?string $search = null)
    {
        $query = Quotation::with('client')
            ->when($from, fn($q) => $q->whereDate('date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('date', '<=', $to))
            ->when($type && $type !== 'all', fn($q) => $q->where('type', $type))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qb) use ($search) {
                    $qb->where('reference_number', 'like', "%{$search}%")
                       ->orWhereHas('client', fn($qc) => $qc->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('date');

        $quotations = $query->get();

        $typeLabel = match($type) {
            'po' => 'Purchase Order (PO)',
            'wo' => 'Work Order (WO)',
            default => 'Semua',
        };

        $pdf = Pdf::loadView('pdf.report-penawaran', [
            'quotations' => $quotations,
            'from' => $from ? \Carbon\Carbon::parse($from)->translatedFormat('d F Y') : '-',
            'to' => $to ? \Carbon\Carbon::parse($to)->translatedFormat('d F Y') : '-',
            'type_label' => $typeLabel,
            'status_label' => $status ? ucfirst($status) : 'Semua',
            'search' => $search,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('report-penawaran.pdf', ['Attachment' => false]);
    }

    public function streamPersetujuan(?string $from, ?string $to, ?string $type, ?string $status = null, ?string $search = null)
    {
        $query = Approval::with(['quotation.client'])
            ->whereHas('quotation', function ($q) {
                $q->whereNull('deleted_at')->where('status', 'approve');
            })
            ->when($from, fn($q) => $q->whereDate('approval_date', '>=', $from))
            ->when($to, fn($q) => $q->whereDate('approval_date', '<=', $to))
            ->when($type && $type !== 'all', fn($q) => $q->whereHas('quotation', fn($qb) => $qb->where('type', $type)))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->whereHas('quotation', function ($qb) use ($search) {
                    $qb->where('reference_number', 'like', "%{$search}%")
                       ->orWhereHas('client', fn($qc) => $qc->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest('approval_date');

        $approvals = $query->get();

        $typeLabel = match($type) {
            'po' => 'Purchase Order (PO)',
            'wo' => 'Work Order (WO)',
            default => 'Semua',
        };

        $pdf = Pdf::loadView('pdf.report-persetujuan', [
            'approvals' => $approvals,
            'from' => $from ? \Carbon\Carbon::parse($from)->translatedFormat('d F Y') : '-',
            'to' => $to ? \Carbon\Carbon::parse($to)->translatedFormat('d F Y') : '-',
            'type_label' => $typeLabel,
            'status_label' => $status ? ucfirst($status) : 'Semua',
            'search' => $search,
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('report-persetujuan.pdf', ['Attachment' => false]);
    }
}
