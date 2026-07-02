<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; box-sizing: border-box; font-family: 'Helvetica', Arial, sans-serif; font-size: 20px; }

        .content { padding: 60px 80px; position: relative; }

        .header-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 700px;
            z-index: -1;
        }

        .header-logo {
            width: 480px;
            height: auto;
            object-fit: contain;
        }

        .header-title {
            position: absolute;
            top: 120px;
            right: 210px;
            font-size: 50px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid black;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 18px;
        }
        .main-table th {
            border: 1px solid black;
            border-bottom: 2px solid black;
            text-align: center;
            font-weight: bold;
            padding: 10px 6px;
        }

        .main-table tbody td {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-bottom: 1px dotted black;
            padding: 8px 10px;
            vertical-align: top;
            height: 35px;
            line-height: 1.3;
        }

        .main-table tbody tr.row-total td {
            border: 2px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
        }

        .no-border, .no-border td { border: none !important; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

@php
    function getImgHelperINV($path) {
        if (!$path) return '';
        try {
            $fullPath = $path;
            if (!file_exists($fullPath)) {
                $fullPath = public_path($path);
            }
            if (!file_exists($fullPath)) {
                $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
            }
            if (!file_exists($fullPath)) {
                $fullPath = \Illuminate\Support\Facades\Storage::disk()->path($path);
            }
            if (file_exists($fullPath)) {
                $type = pathinfo($fullPath, PATHINFO_EXTENSION);
                $data = file_get_contents($fullPath);
                return 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        } catch (\Exception $e) {}
        return '';
    }

    $logoUrl = getImgHelperINV('images/logo-pt.png');
    $curveUrl = getImgHelperINV('images/header-curve.png');
    $stampUrl = getImgHelperINV($invoice->stamp_path);
    $signatureUrl = getImgHelperINV($invoice->signature_path);
@endphp

@if($curveUrl)
<img src="{{ $curveUrl }}" class="header-bg" alt="Curve">
@endif

<div class="content">

    @if($logoUrl)
        <img src="{{ $logoUrl }}" class="header-logo" alt="PT INDO MUTIARA GLOBAL">
    @else
        <h2 style="color: #1e3a8a;">PT. INDO MUTIARA GLOBAL</h2>
    @endif

    <div class="header-title">INVOICE</div>

    <!-- Metadata -->
    <table style="width: 100%; border: none; margin-bottom: 15px; margin-top: 10px;" cellpadding="0" cellspacing="2">
        <tr>
            <td style="width: 150px;">Kepada Yth</td>
            <td style="width: 15px;">:</td>
            <td style="font-weight: bold;">{{ $quotation->client->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nomor</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ $invoice->invoice_number }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Project</td>
            <td style="vertical-align: top;">:</td>
            <td style="font-weight: bold;">{{ $quotation->project_name }}<br>{{ $quotation->project_subname }}</td>
        </tr>
        <tr>
            <td>Reff {{ strtoupper($quotation->type) }} No</td>
            <td>:</td>
            <td style="font-weight: bold;">{{ $invoice->reff_po_number ?? '-' }}</td>
        </tr>
        <tr>
            <td>Contract Sum</td>
            <td>:</td>
            <td style="font-weight: bold;">Rp {{ number_format($invoice->contract_sum, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td style="vertical-align: top;">Lokasi</td>
            <td style="vertical-align: top;">:</td>
            <td style="font-weight: bold;">{{ $quotation->client->address ?? '-' }}</td>
        </tr>
    </table>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Uraian</th>
                <th style="width: 45px;">Qty</th>
                <th style="width: 55px;">Unit</th>
                <th style="width: 130px;">Harga<br>Satuan (Rp)</th>
                <th style="width: 160px;">Jumlah ( Rp )</th>
            </tr>
        </thead>
        <tbody>
            @if($quotation->work_category)
            <tr>
                <td class="text-center">1</td>
                <td style="padding-left: 10px;">
                    Pekerjaan : <span style="font-weight: bold;">{{ strtoupper($quotation->work_category) }}</span>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif

            @if($quotation->service_type)
            <tr>
                <td></td>
                <td class="text-center" style="font-weight: bold; font-style: italic;">
                    {{ $quotation->formatted_service_type }}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="padding-left: 10px;">
                    Dengan rincian sebagai berikut :
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif

                @php 
                    $items = $quotation->items ?? []; 
                    $hasHeader = collect($items)->contains(function($i) {
                        return ($i['type'] ?? 'item_row') === 'header_row';
                    });
                    $numbering = 1;
                @endphp
                @foreach($items as $index => $item)
                    @php 
                        $type = $item['type'] ?? 'item_row'; 
                        $data = $item['data'] ?? $item;
                    @endphp

                    @if($type === 'header_row')
                        <tr>
                            <td class="text-center" style="font-weight: bold;">{{ $numbering++ }}</td>
                            <td style="padding-left: 10px; font-weight: bold; ">
                                {{ $data['uraian'] ?? '' }}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @elseif($type === 'note_row')
                        <tr>
                            <td></td>
                            <td style="padding-left: 10px; font-style: italic;">
                                {{ $data['uraian'] ?? '' }}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @elseif($type === 'discount_row')
                        <tr>
                            <td></td>
                            <td style="padding-left: 10px; font-weight: bold; text-align: left;">
                                {{ $data['uraian'] ?? 'Discount' }}
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-right" style="padding-right: 6px; color: red;">
                                @php $subtotal = $data['sub_total'] ?? -($data['harga_satuan'] ?? 0); @endphp
                                ({{ number_format(abs($subtotal), 0, ',', '.') }})
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="text-center">{{ $hasHeader ? '-' : $numbering++ }}</td>
                            <td style="padding-left: 20px;">{{ $data['uraian'] ?? '' }}</td>
                            <td class="text-center">{{ $data['qty'] ?? 0 }}</td>
                            <td class="text-center">{{ $data['unit'] ?? '' }}</td>
                            <td class="text-right" style="padding-right: 6px;">
                                {{ number_format($data['harga_satuan'] ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-right" style="padding-right: 6px;">
                                @php $subtotal = $data['sub_total'] ?? (($data['qty'] ?? 0) * ($data['harga_satuan'] ?? 0)); @endphp
                                {{ number_format($subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach

            <!-- Empty rows -->
            @for ($i = 0; $i < 2; $i++)
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor

            <!-- For: note row -->
            @if($quotation->project_subname)
            <tr>
                <td></td>
                <td style="padding-left: 10px; font-size: 18px;">
                    For: {{ $quotation->subject_description }}
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif

            <!-- Rounded row -->
            <tr>
                <td></td>
                <td style="padding-left: 10px;">
                    <span style="color: red; font-weight: bold; font-style: italic;">Rounded</span>
                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right" style="padding-right: 10px;">-</td>
            </tr>

            <tr class="row-total">
                <td colspan="5" class="text-left" style="border-right: 1px solid black; border-bottom: 2px solid black; padding-left: 50px;">TOTAL</td>
                <td style="padding: 0; background-color: white; border-bottom: 2px solid black;">
                    <table style="width: 100%; border: none; margin: 0; border-collapse: collapse;">
                        <tr>
                            <td class="no-border" style="text-align: left; padding: 8px; width: 35px;">Rp</td>
                            <td class="no-border" style="text-align: right; padding: 8px 10px;">{{ number_format($invoice->contract_sum, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="row-terbilang">
                <td colspan="6" class="text-left" style="border-right: 2px ; border-bottom: 2px solid black; padding-left: 15px; background-color: #D3D3D3;"><strong>Terbilang : <em>{{ \App\Helpers\Terbilang::rupiah($invoice->contract_sum) }}</em></strong></td>
            </tr>
        </tbody>
    </table>

    {{-- <!-- Terbilang -->
    <div style="margin-top: 5px; margin-bottom: 20px; font-size: 16px;">
        <strong>Terbilang : <em>{{ \App\Helpers\Terbilang::rupiah($invoice->contract_sum) }}</em></strong>
    </div> --}}

    <!-- Payment info -->
    <div style="margin-bottom: 15px;">
        <table style="width: 100%; border: none;" cellpadding="0" cellspacing="2">
            <tr>
                <td style="width: 55%; vertical-align: top;">
                    <strong>Pembayaran mohon ditransfer ke :</strong><br>
                    <table style="border: none; margin-top: 8px;" cellpadding="0" cellspacing="2">
                        <tr>
                            <td style="width: 100px;"><strong>Bank</strong></td>
                            <td style="width: 15px;">:</td>
                            <td><strong>{{ $invoice->bank_name ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Cabang</strong></td>
                            <td>:</td>
                            <td><strong>{{ $invoice->bank_branch ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>No Rek</strong></td>
                            <td>:</td>
                            <td><strong>{{ $invoice->bank_account_number ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>A/N</strong></td>
                            <td>:</td>
                            <td><strong>{{ $invoice->bank_account_name ?? '-' }}</strong></td>
                        </tr>
                    </table>
                </td>
                <td style="width: 45%; vertical-align: top; text-align: right; padding-top: 70px; padding-right: 50px;">
                    Jakarta, {{ \Carbon\Carbon::parse($invoice->date)->translatedFormat('d F Y') }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Signature -->
    <div style="position: relative; width: 350px; height: 180px; margin-top: 10px; float: right;">
        @if($stampUrl)
            <img src="{{ $stampUrl }}" alt="Stempel" style="position: absolute; top: -10px; left: 30px; width: 140px; height: 140px; object-fit: contain; opacity: 0.8; z-index: 1;">
        @endif

        @if($signatureUrl)
            <img src="{{ $signatureUrl }}" alt="Tanda Tangan" style="position: absolute; bottom: 15px; right: 20px; max-width: 200px; max-height: 120px; z-index: 2;">
        @endif

        <div style="position: absolute; bottom: 8px; right: 20px; font-weight: bold; font-size: 18px; text-align: center; width: 250px;">
            <u>{{ strtoupper($invoice->signature_name ?? $quotation->signature_name ?? '...................') }}</u>
        </div>
    </div>

    <div style="clear: both;"></div>

    <!-- Disclaimer -->
    <div style="margin-top: 20px; font-size: 18px; font-style: italic;">
        *Pembayaran dianggap lunas apabila giro/transfer sudah kami terima
    </div>
</div>
</body>
</html>
