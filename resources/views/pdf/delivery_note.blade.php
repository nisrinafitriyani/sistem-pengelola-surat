<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan</title>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; box-sizing: border-box; font-family: 'Helvetica', Arial, sans-serif; font-size: 20px; }

        .content { padding: 60px 80px; position: relative; }

        .header-bg {
            position: absolute;
            top: 0;
            right: 0;
            width: 700px;
            height: 250px;
            object-fit: cover;
            z-index: -1;
        }

        .header-container {
            width: 100%;
            margin-bottom: 50px;
        }

        .header-logo {
            width: 580px;
            height: auto;
            object-fit: cover;
            vertical-align: middle;
        }

        .header-title {
            float: right;
            font-size: 34px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 80px;
            margin-right: 50px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
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
            height: 35px;
            line-height: 1.3;
        }

        .no-border, .no-border td { border: none !important; }

        .address-box { margin-top: -5px; margin-bottom: 30px; line-height: 1.4; font-size: 15px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        /* PENGATURAN AREA TANDA TANGAN (SUDAH DIEDIT AGAR LEBIH DEKAT & AMAN) */
        .signature-block {
            width: 100%;
            margin-top: 30px;
        }
        .signature-block td {
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 5px 10px;
            font-size: 18px;
        }
        
        .sig-info-box {
            display: inline-block;
            text-align: left;
            min-width: 140px;
        }

        /* Jarak tinggi space kosong diperkecil dari 110px ke 65px */
        .signature-block .sig-space {
            height: 65px;
            position: relative;
        }

        /* Pembungkus gambar tanda tangan digital agar posisinya presisi di atas teks nama */
        .sig-img-container {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
        }
    </style>
</head>
<body>

@php
    function getImgHelperDN($path) {
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

    $logoUrl    = getImgHelperDN('images/logo-pt.png');
    $curveUrl   = getImgHelperDN('images/header-curve.png');
    $signatureUrl = getImgHelperDN($deliveryNote->signature_image);
@endphp

@if($curveUrl)
<img src="{{ $curveUrl }}" class="header-bg" alt="Curve">
@endif

<div class="content">

    <div class="header-container clearfix">
        @if($logoUrl)
            <img src="{{ $logoUrl }}" class="header-logo" alt="PT INDO MUTIARA GLOBAL">
        @else
            <h2 style="color: #1e3a8a; float: left; margin: 0;">PT. INDO MUTIARA GLOBAL</h2>
        @endif

        <div class="header-title">SURAT JALAN</div>
    </div>

    <table style="width: 100%; border: none; margin-bottom: 15px;" cellpadding="0" cellspacing="2">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <table style="width: 100%; border: none;" cellpadding="0" cellspacing="2">
                    <tr>
                        <td style="width: 120px;">Kepada Yth</td>
                        <td style="width: 15px;">:</td>
                        <td style="font-weight: bold;">{{ $quotation->client->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Nomor</td>
                        <td>:</td>
                        <td style="font-weight: bold;">{{ $deliveryNote->delivery_number }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Project</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="font-weight: bold;">{{ $quotation->project_name }}<br>{{ $quotation->project_subname }}</td>
                    </tr>
                    <tr>
                        <td>Reff {{ strtoupper($quotation->type) }} No</td>
                        <td>:</td>
                        <td style="font-weight: bold;">{{ $quotation->reference_number }}</td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">Lokasi</td>
                        <td style="vertical-align: top;">:</td>
                        <td style="font-weight: bold;">{{ $quotation->client->address ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            
            <td style="width: 45%; vertical-align: top; padding-left: 30px;">
                <div style="margin-bottom: 12px; font-size: 20px; font-weight: bold;">
                    Jakarta, {{ \Carbon\Carbon::parse($deliveryNote->date)->translatedFormat('d F Y') }}
                </div>

                <table style="width: 100%; border: none;" cellpadding="0" cellspacing="2">
                    <tr>
                        <td style="width: 120px;">Kendaraan</td>
                        <td style="width: 15px;">:</td>
                        <td>{{ $deliveryNote->vehicle_type ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>No. Pol</td>
                        <td>:</td>
                        <td>{{ $deliveryNote->vehicle_plate ?? '' }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>:</td>
                        <td>{{ \Carbon\Carbon::parse($deliveryNote->date)->translatedFormat('d F Y') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-bottom: 10px;">
        Dengan Hormat,<br>
        Dengan ini kami mengirimkan Material {{ $quotation->service_type }} untuk {{ $quotation->client->name ?? '-' }}, {{ $quotation->project_subname }}<br>
        dengan rincian sebagai berikut :
    </div>

    <table class="main-table">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th>Uraian</th>
                <th style="width: 70px;">Jumlah</th>
                <th style="width: 70px;">Satuan</th>
                <th style="width: 120px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @if($quotation->work_category)
            <tr>
                <td></td>
                <td style="padding-left: 10px;">
                    Pekerjaan : <span style="font-weight: bold;">{{ strtoupper($quotation->work_category) }}</span>
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif

            @if($quotation->service_type)
            <tr>
                <td></td>
                <td class="text-left" style= "padding-left: 110px; font-weight: bold; ">
                    {{ $quotation->service_type }}
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="padding-left: 10px;">Dengan rincian sebagai berikut :</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif

            @php $items = $quotation->items ?? []; @endphp
            @foreach($items as $index => $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td style="padding-left: 15px;">{{ $item['uraian'] ?? '' }}</td>
                <td class="text-center">{{ $item['qty'] ?? 0 }}</td>
                <td class="text-center">{{ $item['unit'] ?? '' }}</td>
                <td class="text-center">Terkirim</td>
            </tr>
            @endforeach

            @for ($i = 0; $i < 2; $i++)
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <table class="signature-block" style="border: none;">
        <tr>
            <td>
                <div class="sig-info-box">
                    <strong>Diterima Tgl :</strong><br>
                    <strong>Diterima Jam :</strong>
                </div>
            </td>
            <td>
                <div class="sig-info-box">
                    <strong>Dikirim Tgl :</strong><br>
                    <strong>Dikirim Jam :</strong>
                </div>
            </td>
            <td>
                <div class="sig-info-box">
                    <strong>Dikirim Tgl :</strong><br>
                    <strong>Dikirim Jam :</strong>
                </div>
            </td>
        </tr>
        <tr>
            <td class="sig-space"></td>
            <td class="sig-space"></td>
            <td class="sig-space">
                @if($signatureUrl)
                    <div class="sig-img-container">
                        <img src="{{ $signatureUrl }}" style="max-height: 110px; max-width: 180px; display: block; margin: 0 auto -35px auto; position: relative; z-index: -1;" alt="Tanda Tangan">
                    </div>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding-top: 10px;">
                ( {{ $deliveryNote->receiver_name ?? '...................' }} )<br>
                <strong>Penerima</strong>
            </td>
            <td style="padding-top: 10px;">
                ( {{ $deliveryNote->driver_name ?? '...................' }} )<br>
                <strong>Driver</strong>
            </td>
            <td style="padding-top: 10px;">
                ( {{ strtoupper($deliveryNote->signature_name ?? $quotation->signature_name ?? '...................') }} )<br>
                <strong>{{ $deliveryNote->signature_role ?? 'Adm Kantor' }}</strong>
            </td>
        </tr>
    </table>

    <div style="margin-top: 50px; font-size: 20px;">
        <strong>Segera Lapor Apabila<br>Ada Ketidak Sesuaian</strong><br>
        <table style="border: none; margin-top: 8px;" cellpadding="0" cellspacing="2">
            <tr><td style="width: 100px;"><strong>Lembar 1</strong></td><td>:</td><td><strong>Pengirim</strong></td></tr>
            <tr><td><strong>Lembar 2</strong></td><td>:</td><td><strong>Penerima</strong></td></tr>
            <tr><td><strong>Lembar 3</strong></td><td>:</td><td><strong>Arsip</strong></td></tr>
        </table>
    </div>
</div>
</body>
</html>