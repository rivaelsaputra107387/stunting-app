<?php

namespace App\Http\Controllers\Kelurahan;

use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;
use App\Models\Posyandu;
use App\Services\ConfusionMatrixService;
use App\Services\DecisionTreeService;

class KlasifikasiController extends Controller
{
    /**
     * Show classification page with filters, predictions, and confusion matrix.
     */
    public function index(DecisionTreeService $dtService, ConfusionMatrixService $cmService)
    {
        $posyandus = Posyandu::orderBy('nama')->get();

        $query = Pemeriksaan::with(['balita', 'posyandu']);

        if ($posyanduId = request('posyandu_id')) {
            $query->where('posyandu_id', $posyanduId);
        }
        if ($bulan = request('bulan')) {
            $query->whereMonth('tanggal_pemeriksaan', $bulan);
        }
        if ($tahun = request('tahun')) {
            $query->whereYear('tanggal_pemeriksaan', $tahun);
        }

        $allPemeriksaans = (clone $query)->get();
        $evaluation = $cmService->calculate($allPemeriksaans, $dtService);

        $pemeriksaans = $query->latest('tanggal_pemeriksaan')->paginate(25)->withQueryString();

        return view('kelurahan.klasifikasi', compact('posyandus', 'pemeriksaans', 'evaluation', 'dtService'));
    }

    /**
     * Batch re-classify all examinations using Decision Tree.
     */
    public function proses(DecisionTreeService $dtService)
    {
        $query = Pemeriksaan::with('balita');

        if ($posyanduId = request('posyandu_id')) {
            $query->where('posyandu_id', $posyanduId);
        }
        if ($bulan = request('bulan')) {
            $query->whereMonth('tanggal_pemeriksaan', $bulan);
        }
        if ($tahun = request('tahun')) {
            $query->whereYear('tanggal_pemeriksaan', $tahun);
        }

        $pemeriksaans = $query->get();
        $count = 0;

        foreach ($pemeriksaans as $pemeriksaan) {
            $balita = $pemeriksaan->balita;
            if (!$balita) continue;

            $hasil = $dtService->classify(
                umurBulan: $pemeriksaan->umur_bulan,
                jenisKelamin: $balita->jenis_kelamin,
                tinggiBadan: (float) $pemeriksaan->tinggi_badan,
                beratBadan: (float) $pemeriksaan->berat_badan,
            );

            $pemeriksaan->update([
                'status_stunting'    => $hasil['status'],
                'zscore_tb_u'        => $hasil['zscore'],
                'status_berat_badan' => $hasil['status_berat'],
                'zscore_bb_u'        => $hasil['zscore_bb_u'],
            ]);

            $count++;
        }

        return redirect()->route('kelurahan.klasifikasi.index', request()->query())
            ->with('success', "Berhasil mengklasifikasi ulang {$count} data pemeriksaan.");
    }
}

