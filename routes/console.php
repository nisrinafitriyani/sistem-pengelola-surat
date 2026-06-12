<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Quotation;
use App\Models\Approval;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-permanently delete quotations that have been soft-deleted for more than 7 days
Schedule::call(function () {
    $expiredQuotations = Quotation::onlyTrashed()
        ->where('deleted_at', '<=', now()->subDays(7))
        ->get();

    foreach ($expiredQuotations as $quotation) {
        // Force delete associated approval first
        Approval::withTrashed()
            ->where('quotation_id', $quotation->id)
            ->forceDelete();

        $quotation->forceDelete();
    }
})->daily()->name('cleanup-trashed-quotations')->description('Permanently delete quotations trashed for 7+ days');
