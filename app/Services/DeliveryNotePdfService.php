<?php

namespace App\Services;

use App\Models\DeliveryNote;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryNotePdfService
{
    public function generate(DeliveryNote $deliveryNote): \Barryvdh\DomPDF\PDF
    {
        $deliveryNote->load('approval.quotation.client');

        return Pdf::loadView('pdf.delivery_note', [
            'deliveryNote' => $deliveryNote,
            'quotation' => $deliveryNote->approval->quotation,
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'defaultFont' => 'Helvetica',
            'dpi' => 150,
        ]);
    }

    public function stream(DeliveryNote $deliveryNote)
    {
        $pdf = $this->generate($deliveryNote);
        $filename = 'SJ-' . str_replace('/', '-', $deliveryNote->delivery_number ?? $deliveryNote->id) . '.pdf';

        return $pdf->stream($filename, ['Attachment' => false]);
    }
}
