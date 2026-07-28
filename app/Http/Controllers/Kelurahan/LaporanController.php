<?php

namespace App\Http\Controllers\Kelurahan;

use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;
use App\Models\Posyandu;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    /**
     * Show the report filter page.
     */
    public function index()
    {
        $posyandus = Posyandu::orderBy('nama')->get();

        return view('kelurahan.laporan', compact('posyandus'));
    }

    /**
     * Export report as PDF.
     */
    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('laporan.pdf', $data);
        $pdf->setPaper('A4', 'landscape');

        $filename = 'laporan_stunting_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export report as Excel.
     */
    public function exportExcel()
    {
        $data = $this->getReportData();
        $filename = 'laporan_stunting_' . now()->format('Y-m-d') . '.xlsx';
        $path = storage_path('app/public/' . $filename);

        // Use OpenSpout writer directly for full row control (titles + data)
        $writer = new \OpenSpout\Writer\XLSX\Writer();
        $writer->openToFile($path);

        // Helper to create a row
        $makeRow = fn(array $values) => \OpenSpout\Common\Entity\Row::fromValues($values);

        // ── KOP SURAT ──────────────────────────────────────────────────
        $writer->addRow($makeRow(['PEMERINTAH KOTA BANDUNG']));
        $writer->addRow($makeRow(['KECAMATAN BABAKAN CIPARAY - KELURAHAN SUKAHAJI']));
        $writer->addRow($makeRow(['Sistem eSStunting — Laporan Klasifikasi Status Stunting Balita']));
        $writer->addRow($makeRow([''])); // blank line

        // ── META INFO ──────────────────────────────────────────────────
        $writer->addRow($makeRow(['Posyandu', ': ' . $data['posyanduNama']]));
        $writer->addRow($makeRow(['Periode', ': ' . $data['periode']]));
        $writer->addRow($makeRow(['Tanggal Cetak', ': ' . $data['tanggalCetak']]));
        $writer->addRow($makeRow(['Total Data', ': ' . $data['pemeriksaans']->count() . ' pemeriksaan']));
        $writer->addRow($makeRow([''])); // blank line

        // ── RINGKASAN STATUS STUNTING ──────────────────────────────────
        $writer->addRow($makeRow(['RINGKASAN STATUS STUNTING']));
        $writer->addRow($makeRow(['Normal', 'Risk of Stunting', 'Stunting']));
        $writer->addRow($makeRow([
            $data['statusCounts']['normal'],
            $data['statusCounts']['risk'],
            $data['statusCounts']['stunting'],
        ]));
        $writer->addRow($makeRow([''])); // blank line

        // ── HEADER KOLOM DATA ──────────────────────────────────────────
        $writer->addRow($makeRow([
            'No',
            'Posyandu',
            'RW',
            'Nama Balita',
            'NIK',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Umur (Bulan)',
            'Tanggal Pemeriksaan',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Z-Score TB/U',
            'Status Stunting',
            'Z-Score BB/U',
            'Status Gizi BB',
            'Catatan',
        ]));

        // ── BARIS DATA ─────────────────────────────────────────────────
        $no = 1;
        foreach ($data['pemeriksaans'] as $p) {
            $writer->addRow($makeRow([
                $no++,
                $p->posyandu->nama ?? '-',
                $p->posyandu->rw ?? '-',
                $p->balita->nama ?? '-',
                $p->balita->nik ?? '-',
                $p->balita->jenis_kelamin ?? '-',
                $p->balita->tanggal_lahir?->format('d/m/Y') ?? '-',
                $p->umur_bulan,
                $p->tanggal_pemeriksaan->format('d/m/Y'),
                (float) $p->tinggi_badan,
                (float) $p->berat_badan,
                (float) $p->zscore_tb_u,
                $p->status_label,
                $p->zscore_bb_u !== null ? (float) $p->zscore_bb_u : '-',
                $p->status_berat_badan ?? '-',
                $p->catatan ?? '-',
            ]));
        }

        $writer->close();

        return response()->download($path, $filename)->deleteFileAfterSend();
    }

    /**
     * Get filtered report data.
     *
     * @return array
     */
    private function getReportData(): array
    {
        $query = Pemeriksaan::with(['balita', 'posyandu']);

        if ($posyanduId = request('posyandu_id')) {
            $query->where('posyandu_id', $posyanduId);
        }
        if ($status = request('status_stunting')) {
            $query->where('status_stunting', $status);
        }
        if ($bulan = request('bulan')) {
            $query->whereMonth('tanggal_pemeriksaan', $bulan);
        }
        if ($tahun = request('tahun')) {
            $query->whereYear('tanggal_pemeriksaan', $tahun);
        }

        $pemeriksaans = $query->latest('tanggal_pemeriksaan')->get();

        $posyanduNama = 'Semua Posyandu';
        if ($posyanduId) {
            $posyanduNama = Posyandu::find($posyanduId)?->nama ?? 'Semua Posyandu';
        }

        $periode = '';
        if (request('bulan') && request('tahun')) {
            $periode = \Carbon\Carbon::createFromDate(request('tahun'), request('bulan'), 1)
                ->translatedFormat('F Y');
        } elseif (request('tahun')) {
            $periode = 'Tahun ' . request('tahun');
        } else {
            $periode = 'Semua Periode';
        }

        return [
            'pemeriksaans'  => $pemeriksaans,
            'posyanduNama'  => $posyanduNama,
            'periode'       => $periode,
            'tanggalCetak'  => now()->translatedFormat('d F Y'),
            'statusCounts'  => [
                'normal'   => $pemeriksaans->where('status_stunting', 'Normal')->count(),
                'risk'     => $pemeriksaans->where('status_stunting', 'Risk of Stunting')->count(),
                'stunting' => $pemeriksaans->where('status_stunting', 'Stunting')->count(),
            ],
        ];
    }
}
