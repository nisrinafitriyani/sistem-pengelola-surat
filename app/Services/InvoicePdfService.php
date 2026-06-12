<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generate(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        $invoice->load('approval.quotation.client');

        return Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'quotation' => $invoice->approval->quotation,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Helvetica',
            'dpi' => 150,
        ]);
    }

    public function stream(Invoice $invoice)
    {
        $pdf = $this->generate($invoice);
        $filename = 'INV-' . str_replace('/', '-', $invoice->invoice_number ?? $invoice->id) . '.pdf';

        return $pdf->stream($filename, ['Attachment' => false]);
    }
}
