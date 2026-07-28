<?php

namespace App\Http\Controllers\Kelurahan;

use App\Http\Controllers\Controller;
use App\Models\Pemeriksaan;
use App\Models\Posyandu;
use App\Services\ConfusionMatrixService;
use App\Services\DecisionTreeService;
use App\Services\C45Service;

class KlasifikasiController extends Controller
{
    /**
     * Show classification page with filters, predictions, and confusion matrix.
     */
    public function index(DecisionTreeService $dtService, ConfusionMatrixService $cmService, C45Service $c45Service)
    {
        $posyandus = Posyandu::orderBy('nama')->get();

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

        $allPemeriksaans = (clone $query)->get();
        $evaluation = $cmService->calculate($allPemeriksaans, $c45Service);

        $pemeriksaans = $query->latest('tanggal_pemeriksaan')->paginate(25)->withQueryString();

        return view('kelurahan.klasifikasi', compact('posyandus', 'pemeriksaans', 'evaluation', 'dtService', 'c45Service'));
    }

    /**
     * Batch re-classify all examinations using Decision Tree.
     */
    public function proses(DecisionTreeService $dtService, C45Service $c45Service)
    {
        $query = Pemeriksaan::with('balita');

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

        // 1. Train the model with all available data (or data matching the filter)
        $c45Service->train();

        $pemeriksaans = $query->get();
        $count = 0;

        foreach ($pemeriksaans as $pemeriksaan) {
            $balita = $pemeriksaan->balita;
            if (!$balita) continue;

            // Z-Score calculation
            $hasil = $dtService->classify(
                umurBulan: $pemeriksaan->umur_bulan,
                jenisKelamin: $balita->jenis_kelamin,
                tinggiBadan: (float) $pemeriksaan->tinggi_badan,
                beratBadan: (float) $pemeriksaan->berat_badan,
            );

            // C4.5 Prediction
            $dtPrediction = $c45Service->predict(
                $pemeriksaan->umur_bulan,
                $balita->jenis_kelamin,
                (float) $pemeriksaan->tinggi_badan,
                (float) $pemeriksaan->berat_badan
            );

            $pemeriksaan->update([
                'status_stunting'    => $hasil['status'],
                'status_dt'          => $dtPrediction,
                'zscore_tb_u'        => $hasil['zscore'],
                'status_berat_badan' => $hasil['status_berat'],
                'zscore_bb_u'        => $hasil['zscore_bb_u'],
            ]);

            $count++;
        }

        return redirect()->route('kelurahan.klasifikasi.index', request()->query())
            ->with('success', "Berhasil melatih model C4.5 dan memprediksi ulang {$count} data pemeriksaan.");
    }
}

