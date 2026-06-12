<?php

namespace App\Observers;

use App\Models\Approval;
use App\Models\Handover;
use App\Models\DeliveryNote;
use App\Models\Invoice;

class ApprovalObserver
{
    /**
     * Handle the Approval "created" event.
     */
    public function created(Approval $approval): void
    {
        if ($approval->status === 'completed') {
            $this->createDownstreamDocuments($approval);
        }
    }

    /**
     * Handle the Approval "updating" event.
     * When status changes to 'completed', auto-create downstream documents.
     * When status reverts to 'pending', soft-delete downstream documents.
     */
    public function updating(Approval $approval): void
    {
        if (!$approval->isDirty('status')) {
            return;
        }

        $newStatus = $approval->status;
        $oldStatus = $approval->getOriginal('status');

        // Transition TO completed → create documents based on quotation type
        if ($newStatus === 'completed') {
            $this->createDownstreamDocuments($approval);
        }

        // Transition FROM completed TO pending → soft-delete downstream documents
        if ($oldStatus === 'completed' && $newStatus === 'pending') {
            $this->softDeleteDownstreamDocuments($approval);
        }
    }

    /**
     * Handle the Approval "deleting" event.
     * Cascade soft-delete to downstream documents.
     */
    public function deleting(Approval $approval): void
    {
        $quotation = $approval->quotation;
        if (!$quotation) return;

        $this->softDeleteDownstreamDocuments($approval);
    }

    /**
     * Handle the Approval "restoring" event.
     * If status is completed, restore downstream documents too.
     */
    public function restoring(Approval $approval): void
    {
        if ($approval->status === 'completed') {
            $this->restoreDownstreamDocuments($approval);
        }
    }

    private function createDownstreamDocuments(Approval $approval): void
    {
        $quotation = $approval->quotation;
        if (!$quotation) return;

        $type = $quotation->type; // 'wo' or 'po'

        // Generate auto-number helpers
        $monthRoman = $this->toRoman((int) now()->format('m'));
        $year = now()->format('Y');
        $day = now()->format('d');

        if ($type === 'wo') {
            // WO → Berita Acara + Invoice
            $this->createOrRestoreHandover($approval, $quotation, $monthRoman, $year, $day);
        } else {
            // PO → Surat Jalan + Invoice
            $this->createOrRestoreDeliveryNote($approval, $quotation, $monthRoman, $year, $day);
        }

        // Invoice for both types
        $this->createOrRestoreInvoice($approval, $quotation, $monthRoman, $year, $day);
    }

    private function createOrRestoreHandover(Approval $approval, \App\Models\Quotation $quotation, string $monthRoman, string $year, string $day): void
    {
        $existing = Handover::withTrashed()->where('approval_id', $approval->id)->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            return;
        }

        $abbr = $quotation->client ? $quotation->client->abbreviation : 'KI';
        $seq = Handover::withTrashed()->whereYear('date', now()->year)->count() + 1;
        $refNumber = str_pad($seq, 3, '0', STR_PAD_LEFT) . '/BAST/IMG-' . $abbr . '/' . $monthRoman . '/' . $year . '/' . $day;

        Handover::create([
            'approval_id' => $approval->id,
            'reference_number' => $refNumber,
            'date' => now()->toDateString(),
        ]);
    }

    private function createOrRestoreDeliveryNote(Approval $approval, \App\Models\Quotation $quotation, string $monthRoman, string $year, string $day): void
    {
        $existing = DeliveryNote::withTrashed()->where('approval_id', $approval->id)->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            return;
        }

        $abbr = $quotation->client ? $quotation->client->abbreviation : 'KI';
        $seq = DeliveryNote::withTrashed()->whereYear('date', now()->year)->count() + 1;
        $deliveryNumber = str_pad($seq, 3, '0', STR_PAD_LEFT) . '/SJ/IMG-' . $abbr . '/' . $monthRoman . '/' . $year . '/' . $day;

        DeliveryNote::create([
            'approval_id' => $approval->id,
            'delivery_number' => $deliveryNumber,
            'date' => now()->toDateString(),
        ]);
    }

    private function createOrRestoreInvoice(Approval $approval, \App\Models\Quotation $quotation, string $monthRoman, string $year, string $day): void
    {
        $existing = Invoice::withTrashed()->where('approval_id', $approval->id)->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            return;
        }

        $abbr = $quotation->client ? $quotation->client->abbreviation : 'KI';
        $seq = Invoice::withTrashed()->whereYear('date', now()->year)->count() + 1;
        $invoiceNumber = str_pad($seq, 3, '0', STR_PAD_LEFT) . '/INV/IMG-' . $abbr . '/' . $monthRoman . '/' . $year . '/' . $day;

        // Auto-generate Reff PO/WO No
        $typeCode = strtoupper($quotation->type); // 'PO' or 'WO'
        $reffSeq = str_pad($seq, 4, '0', STR_PAD_LEFT);
        $month = now()->format('m');
        $reffNumber = $reffSeq . '/' . $typeCode . '/IMG-' . $abbr . '/' . $month . '/' . $year;

        Invoice::create([
            'approval_id' => $approval->id,
            'invoice_number' => $invoiceNumber,
            'date' => now()->toDateString(),
            'reff_po_number' => $reffNumber,
            'contract_sum' => $quotation->total_amount,
            'signature_name' => $quotation->signature_name,
            'signature_role' => $quotation->signature_role,
        ]);
    }

    private function softDeleteDownstreamDocuments(Approval $approval): void
    {
        $quotation = $approval->quotation;
        if (!$quotation) return;

        // Soft-delete handover if exists
        $handover = Handover::where('approval_id', $approval->id)->first();
        if ($handover) $handover->delete();

        // Soft-delete delivery note if exists
        $deliveryNote = DeliveryNote::where('approval_id', $approval->id)->first();
        if ($deliveryNote) $deliveryNote->delete();

        // Soft-delete invoice if exists
        $invoice = Invoice::where('approval_id', $approval->id)->first();
        if ($invoice) $invoice->delete();
    }

    private function restoreDownstreamDocuments(Approval $approval): void
    {
        Handover::withTrashed()->where('approval_id', $approval->id)->first()?->restore();
        DeliveryNote::withTrashed()->where('approval_id', $approval->id)->first()?->restore();
        Invoice::withTrashed()->where('approval_id', $approval->id)->first()?->restore();
    }

    /**
     * Convert integer to Roman numeral.
     */
    private function toRoman(int $number): string
    {
        $map = [
            'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400,
            'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40,
            'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1,
        ];

        $result = '';
        foreach ($map as $roman => $value) {
            while ($number >= $value) {
                $result .= $roman;
                $number -= $value;
            }
        }
        return $result;
    }
}
