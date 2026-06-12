<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report Persetujuan</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a1a2e; padding: 25px 30px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 14pt; color: #0d47a1; margin-bottom: 4px; }
        .header p { font-size: 9pt; color: #666; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .meta-table td { padding: 3px 5px; font-size: 9pt; }
        .meta-table .label { width: 120px; font-weight: bold; }
        .meta-table .sep { width: 10px; }
        hr { border: none; border-top: 2px solid #0d47a1; margin-bottom: 15px; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table th { background-color: #0d47a1; color: #fff; padding: 7px 5px; font-size: 8pt; text-align: center; border: 1px solid #0d47a1; }
        .data-table td { padding: 5px; font-size: 8pt; border: 1px solid #ddd; }
        .data-table .col-no { text-align: center; width: 25px; }
        .data-table .col-center { text-align: center; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 25px; text-align: center; font-size: 7pt; color: #999; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORT PERSETUJUAN</h1>
        <p>PT. INDO MUTIARA GLOBAL</p>
    </div>
    <hr>

    <table class="meta-table">
        <tr><td class="label">Periode</td><td class="sep">:</td><td>{{ $from }} s/d {{ $to }}</td></tr>
        <tr><td class="label">Filter Tipe</td><td class="sep">:</td><td>{{ $type_label }}</td></tr>
        <tr><td class="label">Filter Status</td><td class="sep">:</td><td>{{ $status_label ?? 'Semua' }}</td></tr>
        @if(!empty($search))
        <tr><td class="label">Pencarian</td><td class="sep">:</td><td>"{{ $search }}"</td></tr>
        @endif
        <tr><td class="label">Tanggal Cetak</td><td class="sep">:</td><td>{{ now()->translatedFormat('d F Y H:i') }}</td></tr>
        <tr><td class="label">Total Data</td><td class="sep">:</td><td>{{ count($approvals) }} persetujuan</td></tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th>No. Surat</th>
                <th>Klien</th>
                <th>Proyek</th>
                <th>Tipe</th>
                <th>Tgl Persetujuan</th>
                <th>PIC Klien</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($approvals as $i => $a)
            <tr>
                <td class="col-no">{{ $i + 1 }}</td>
                <td>{{ $a->quotation->reference_number ?? '-' }}</td>
                <td>{{ $a->quotation->client->name ?? '-' }}</td>
                <td>{{ $a->quotation->project_name ?? '-' }}</td>
                <td class="col-center">{{ strtoupper($a->quotation->type ?? '-') }}</td>
                <td class="col-center">{{ $a->approval_date->format('d/m/Y') }}</td>
                <td>{{ $a->client_pic_name }}</td>
                <td class="col-center">{{ ['pending' => 'Berjalan', 'completed' => 'Selesai'][$a->status] ?? ucfirst($a->status) }}</td>
                <td style="text-align: right;">Rp {{ number_format($a->quotation->total_amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center; padding:15px;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f5f5f5;">
                <td colspan="8" style="text-align: right; padding: 7px 5px; font-weight: bold;">Grand Total</td>
                <td style="text-align: right; font-weight: bold;">Rp {{ number_format($approvals->sum(fn($a) => $a->quotation->total_amount ?? 0), 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">Digenerate oleh SAPP — Sistem Administrasi Penawaran & Persetujuan</div>
</body>
</html>
