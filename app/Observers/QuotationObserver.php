<?php

namespace App\Observers;

use App\Models\Quotation;
use App\Models\Approval;

class QuotationObserver
{
    /**
     * Handle the Quotation "updating" event.
     */
    public function updating(Quotation $quotation): void
    {
        if (!$quotation->isDirty('status')) {
            return;
        }

        $newStatus = $quotation->status;
        $oldStatus = $quotation->getOriginal('status');

        // Transition TO approve → create or restore approval
        if ($newStatus === 'approve') {
            $this->createOrRestoreApproval($quotation);
        }

        // Transition FROM approve TO draft/reject → soft-delete approval
        if ($oldStatus === 'approve' && in_array($newStatus, ['draft', 'reject'])) {
            $this->softDeleteApproval($quotation);
        }
    }

    /**
     * Handle the Quotation "deleting" event.
     * When quotation is deleted, also delete approval.
     */
    public function deleting(Quotation $quotation): void
    {
        $approval = Approval::withTrashed()
            ->where('quotation_id', $quotation->id)
            ->first();

        if ($approval) {
            if ($quotation->isForceDeleting()) {
                $approval->forceDelete();
            } else {
                $approval->delete();
            }
        }
    }

    /**
     * Handle the Quotation "restoring" event.
     * When quotation is restored AND status is approve, restore approval too.
     */
    public function restoring(Quotation $quotation): void
    {
        if ($quotation->status === 'approve') {
            $approval = Approval::withTrashed()
                ->where('quotation_id', $quotation->id)
                ->first();

            if ($approval && $approval->trashed()) {
                $approval->restore();
            }
        }
    }

    private function createOrRestoreApproval(Quotation $quotation): void
    {
        $trashedApproval = Approval::withTrashed()
            ->where('quotation_id', $quotation->id)
            ->first();

        if ($trashedApproval) {
            if ($trashedApproval->trashed()) {
                $trashedApproval->restore();
            }
            return;
        }

        $dateObj = now();
        $seq = Approval::withTrashed()
            ->whereYear('approval_date', $dateObj->year)
            ->count() + 1;
        $monthRoman = $this->toRoman((int) $dateObj->month);
        $year = $dateObj->format('Y');
        $abbr = $quotation->client ? $quotation->client->abbreviation : 'KI';
        
        $approvalRefNo = str_pad($seq, 3, '0', STR_PAD_LEFT) . '/APP/IMG-' . $abbr . '/' . $monthRoman . '/' . $year . '/' . $dateObj->format('d');

        Approval::create([
            'quotation_id' => $quotation->id,
            'reference_number' => $approvalRefNo,
            'approval_date' => $dateObj->toDateString(),
            'client_pic_name' => $quotation->client->pic_name ?? $quotation->client->name,
            'status' => 'pending',
        ]);
    }

    private function softDeleteApproval(Quotation $quotation): void
    {
        $approval = $quotation->approval;
        if ($approval) {
            $approval->delete();
        }
    }

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
