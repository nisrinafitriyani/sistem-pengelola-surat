<?php

namespace App\Services;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;

class QuotationPdfService
{
    public function generate(Quotation $quotation): \Barryvdh\DomPDF\PDF
    {
        $quotation->load('client');

        return Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Helvetica',
            'dpi' => 150,
        ]);
    }

    public function stream(Quotation $quotation)
    {
        $pdf = $this->generate($quotation);
        $filename = 'SPH-' . str_replace('/', '-', $quotation->reference_number) . '.pdf';

        // Set Attachment => false to force browser preview instead of automatic direct download
        return $pdf->stream($filename, ['Attachment' => false]);
    }
}
