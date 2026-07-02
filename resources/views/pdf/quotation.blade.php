<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Penawaran</title>
    <style>
        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
            box-sizing: border-box;
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 20px;
        }

        .content {
            padding: 60px 80px;
            position: relative;
        }

        .header-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 700px;
            height: 250px;
            object-fit: cover;
            z-index: -1;
        }

        .header-logo {
            width: 580px;
            height: auto;
            /* object-fit: contain; */
            object-fit: cover;
        }

        .header-title {
            position: absolute;
            top: 130px;
            right: 190px;
            font-size: 34px;
            font-weight: 500;
            text-transform: uppercase;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid black;
            margin-top: 20px;
            margin-bottom: 40px;
            font-size: 20px;
        }

        .main-table th {
            border: 1px solid black;
            border-bottom: 2px solid black;
            text-align: center;
            font-weight: bold;
            padding: 12px 6px;
        }

        .main-table tbody td {
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-bottom: 1px dotted black;
            padding: 10px 12px;
            vertical-align: top;
            height: 40px;
            line-height: 1.3;
        }

        .main-table tbody tr.row-total td {
            border: 2px solid black;
            border-left: 1px solid black;
            border-right: 1px solid black;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 12px;
        }

        .no-border,
        .no-border td {
            border: none !important;
        }

        .address-box {
            margin-top: -5px;
            margin-bottom: 30px;
            line-height: 1.4;
            font-size: 18px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    @php
        function getImgHelper($path)
        {
            if (!$path)
                return '';
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
            } catch (\Exception $e) {
            }
            return '';
        }

        $logoUrl = getImgHelper('images/logo-pt.png');
        $curveUrl = getImgHelper('images/header-curve.png');
        $stampUrl = getImgHelper($quotation->stamp_path);
        $signatureUrl = getImgHelper($quotation->signature_path);
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

        <div class="header-title">SURAT PENAWARAN</div>

        <div class="address-box">
            Jl Pemuda No 65<br>
            Rawamangun, Kota Jakarta Timur<br>
            Phone 021-22471134, Email: indo.mutiara.global@gmail.com
            <hr style="border-top: 1px solid black; margin-top: 5px; margin-bottom: 5px;">
        </div>

        <table style="width: 100%; border: none; margin-bottom: 10px;" cellpadding="0" cellspacing="2">
            <tr>
                <td style="width: 120px;">Kepada Yth</td>
                <td style="width: 15px;">:</td>
                <td style="font-weight: bold;">{{ $quotation->client->name ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nomor</td>
                <td>:</td>
                <td style="font-weight: bold;">{{ $quotation->reference_number }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td style="font-weight: bold;">{{ \Carbon\Carbon::parse($quotation->date)->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Project</td>
                <td style="vertical-align: top;">:</td>
                <td style="font-weight: bold;">{{ $quotation->project_name }}<br>{{ $quotation->project_subname }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Lokasi</td>
                <td style="vertical-align: top;">:</td>
                <td style="font-weight: bold;">{{ $quotation->client->address ?? '-' }}</td>
            </tr>
        </table>

        <div style="margin-bottom: 10px;">
            Dengan hormat,<br>
            Bersama ini kami mengajukan Penawaran {{ $quotation->formatted_service_type }}
            {{ $quotation->subject_description }}.<br>
            Dengan spesifikasi sebagai berikut :
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Uraian</th>
                    <th style="width: 30px;">Qty</th>
                    <th style="width: 35px;">Unit</th>
                    <th style="width: 80px;">Harga<br>Satuan (Rp)</th>
                    <th style="width: 105px;">Jumlah ( Rp )</th>
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
                        <td class="text-left" style= "padding-left: 110px; font-weight: bold; ">
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
                            <td style="padding-left: 10px; font-weight: bold;">
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

                <tr class="row-total">
                    <td colspan="5" class="text-left"
                        style="border-right: 1px solid black; border-bottom: 2px solid black; padding-left: 50px;">TOTAL
                    </td>
                    <td style="padding: 0; background-color: white; border-bottom: 2px solid black;">
                        <table style="width: 100%; border: none; margin: 0; border-collapse: collapse;">
                            <tr>
                                <td class="no-border" style="text-align: left; padding: 6px; width: 25px;">Rp</td>
                                <td class="no-border" style="text-align: right; padding: 6px 6px;">
                                    {{ number_format($quotation->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        <div style="margin-top: 30px;">
            Demikian surat penawaran ini kami sampaikan, sebagai bahan pertimbangan pekerjaan
            {{ $quotation->formatted_service_type }} {{ $quotation->subject_description }}.<br><br>
            Terima kasih atas perhatian dan kerjasamanya.<br>
            Hormat kami,
        </div>

        <div style="position: relative; width: 350px; height: 200px; margin-top: 20px;">
            @if($stampUrl)
                <img src="{{ $stampUrl }}" alt="Stempel"
                    style="position: absolute; top: -10px; left: -10px; width: 150px; height: 150px; object-fit: contain; opacity: 0.8; z-index: 1;">
            @endif

            @if($signatureUrl)
                <img src="{{ $signatureUrl }}" alt="Tanda Tangan"
                    style="position: absolute; top: 20px; left: 30px; width: 220px; height: 120px; object-fit: contain; z-index: 2;">
            @endif

            <div style="position: absolute; bottom: 35px; left: 0; width: 280px; border-bottom: 2px solid black;"></div>
            <div style="position: absolute; bottom: 10px; left: 0; font-weight: bold; font-size: 17px;">
                <u>{{ strtoupper($quotation->signature_name) }}</u>
            </div>
            <div style="position: absolute; bottom: -15px; left: 0; font-size: 17px;">{{ $quotation->signature_role }}
            </div>
        </div>
    </div>
</body>

</html>