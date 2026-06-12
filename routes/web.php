<?php

use Illuminate\Support\Facades\Route;
use App\Services\QuotationPdfService;
use App\Services\DeliveryNotePdfService;
use App\Services\InvoicePdfService;
use App\Services\ReportPdfService;
use App\Models\Quotation;
use App\Models\DeliveryNote;
use App\Models\Invoice;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/admin');
    }

    return redirect('/admin/login');
});

Route::get('/quotation/{quotation}/pdf', function (Quotation $quotation) {
    return app(QuotationPdfService::class)->stream($quotation);
})->name('quotation.pdf.download')->middleware(['web']);

Route::get('/delivery-note/{deliveryNote}/pdf', function (DeliveryNote $deliveryNote) {
    return app(DeliveryNotePdfService::class)->stream($deliveryNote);
})->name('delivery-note.pdf')->middleware(['web']);

Route::get('/invoice/{invoice}/pdf', function (Invoice $invoice) {
    return app(InvoicePdfService::class)->stream($invoice);
})->name('invoice.pdf')->middleware(['web']);

Route::get('/report/penawaran/pdf', function () {
    return app(ReportPdfService::class)->streamPenawaran(
        request('from'),
        request('to'),
        request('type'),
        request('status'),
        request('search')
    );
})->name('report.penawaran.pdf')->middleware(['web']);

Route::get('/report/persetujuan/pdf', function () {
    return app(ReportPdfService::class)->streamPersetujuan(
        request('from'),
        request('to'),
        request('type'),
        request('status'),
        request('search')
    );
})->name('report.persetujuan.pdf')->middleware(['web']);

// Serve private attachment files
Route::get('/attachment/{path}', function (string $path) {
    $fullPath = \Illuminate\Support\Facades\Storage::disk('local')->path($path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('attachment.serve')->middleware(['web', 'auth']);
