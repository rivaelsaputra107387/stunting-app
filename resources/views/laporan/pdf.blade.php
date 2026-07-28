<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stunting — Kelurahan Sukahaji</title>
    <style>
        h1, h2, p { margin: 0; padding: 0; }
        @page { margin: 1.5cm 2.0cm; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10pt; color: #333; }

        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 14pt; font-weight: bold; margin-bottom: 2px; }
        .header h2 { font-size: 12pt; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 9pt; color: #666; }

        .meta { margin-bottom: 15px; }
        .meta table { width: 100%; }
        .meta td { padding: 2px 0; font-size: 10pt; }
        .meta td:first-child { font-weight: bold; width: 150px; }

        .summary { display: flex; margin-bottom: 15px; }
        .summary-box { text-align: center; padding: 8px; border: 1px solid #ddd; margin-right: 10px; border-radius: 4px; }
        .summary-box .number { font-size: 16pt; font-weight: bold; }
        .summary-box .label { font-size: 8pt; color: #666; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.data th { background: #f0f0f0; border: 1px solid #ccc; padding: 6px 8px; font-size: 9pt; text-align: left; }
        table.data td { border: 1px solid #ddd; padding: 5px 8px; font-size: 9pt; }
        table.data tr:nth-child(even) { background: #fafafa; }

        .status-normal { color: #059669; font-weight: bold; }
        .status-pendek { color: #d97706; font-weight: bold; }
        .status-sangat-pendek { color: #dc2626; font-weight: bold; }

        .bb-normal { color: #059669; font-weight: bold; }
        .bb-kurang { color: #d97706; font-weight: bold; }
        .bb-sangat-kurang { color: #dc2626; font-weight: bold; }
        .bb-lebih { color: #2563eb; font-weight: bold; }

        .footer-table { width: 100%; margin-top: 40px; border-collapse: collapse; border: none; }
        .footer-table td { border: none; padding: 0; font-size: 9pt; }
        .footer-table .signature-box { width: 250px; text-align: center; line-height: 1.5; }
        .footer-table .signature-space { height: 55px; }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    {{-- Header / Kop Surat --}}
    <div class="header">
        <h1>PEMERINTAH KOTA BANDUNG</h1>
        <h2>KECAMATAN BABAKAN CIPARAY - KELURAHAN SUKAHAJI</h2>
        <p>Sistem eSStunting — Laporan Klasifikasi Status Stunting Balita</p>
    </div>

    {{-- Meta info --}}
    <div class="meta">
        <table>
            <tr><td>Posyandu</td><td>: {{ $posyanduNama }}</td></tr>
            <tr><td>Periode</td><td>: {{ $periode }}</td></tr>
            <tr><td>Tanggal Cetak</td><td>: {{ $tanggalCetak }}</td></tr>
            <tr><td>Total Data</td><td>: {{ $pemeriksaans->count() }} pemeriksaan</td></tr>
        </table>
    </div>

    {{-- Summary --}}
    <table style="width: 100%; margin-bottom: 15px;">
        <tr>
            <td style="width: 33%; text-align: center; border: 1px solid #ddd; padding: 8px;">
                <div style="font-size: 14pt; font-weight: bold; color: #059669;">{{ $statusCounts['normal'] }}</div>
                <div style="font-size: 8pt; color: #666;">Normal</div>
            </td>
            <td style="width: 33%; text-align: center; border: 1px solid #ddd; padding: 8px;">
                <div style="font-size: 14pt; font-weight: bold; color: #d97706;">{{ $statusCounts['risk'] }}</div>
                <div style="font-size: 8pt; color: #666;">Risk of Stunting</div>
            </td>
            <td style="width: 34%; text-align: center; border: 1px solid #ddd; padding: 8px;">
                <div style="font-size: 14pt; font-weight: bold; color: #dc2626;">{{ $statusCounts['stunting'] }}</div>
                <div style="font-size: 8pt; color: #666;">Stunting</div>
            </td>
        </tr>
    </table>

    {{-- Data Table --}}
    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Posyandu</th>
                <th>Nama Balita</th>
                <th>JK</th>
                <th>Umur</th>
                <th>TB (cm)</th>
                <th>BB (kg)</th>
                <th>Z-Score TB/U</th>
                <th>Status Stunting</th>
                <th>Z-Score BB/U</th>
                <th>Status Gizi BB</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemeriksaans as $i => $p)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $p->posyandu->nama ?? '-' }}</td>
                <td>{{ $p->balita->nama ?? '-' }}</td>
                <td>{{ $p->balita->jenis_kelamin ?? '-' }}</td>
                <td>{{ $p->umur_bulan }} bln</td>
                <td>{{ $p->tinggi_badan }}</td>
                <td>{{ $p->berat_badan }}</td>
                <td>{{ number_format($p->zscore_tb_u, 2) }}</td>
                <td class="{{ $p->status_stunting === 'Normal' ? 'status-normal' : ($p->status_stunting === 'Risk of Stunting' ? 'status-pendek' : 'status-sangat-pendek') }}">
                    {{ $p->status_label }}
                </td>
                <td>{{ $p->zscore_bb_u !== null ? number_format($p->zscore_bb_u, 2) : '-' }}</td>
                <td class="{{ match($p->status_berat_badan) { 'Normal' => 'bb-normal', 'Kurang' => 'bb-kurang', 'Sangat Kurang' => 'bb-sangat-kurang', 'Risiko Berat Badan Lebih' => 'bb-lebih', default => '' } }}">
                    {{ $p->status_berat_badan ?? '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer / Tanda Tangan --}}
    <table class="footer-table">
        <tr>
            <td></td>
            <td class="signature-box">
                <p>Kelurahan Sukahaji, {{ $tanggalCetak }}</p>
                <p>Mengetahui,</p>
                <div class="signature-space"></div>
                <p><strong>___________________________</strong></p>
                <p style="margin-top: 4px; font-weight: bold;">Lurah Sukahaji</p>
            </td>
        </tr>
    </table>
</body>
</html>
