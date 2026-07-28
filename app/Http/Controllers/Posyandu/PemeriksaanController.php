<?php

namespace App\Http\Controllers\Posyandu;

use App\Http\Controllers\Controller;
use App\Http\Requests\PemeriksaanRequest;
use App\Models\Balita;
use App\Models\Pemeriksaan;
use App\Services\DecisionTreeService;

class PemeriksaanController extends Controller
{
    /**
     * Display a listing of pemeriksaans.
     */
    public function index()
    {
        $query = Pemeriksaan::with('balita')->latest('tanggal_pemeriksaan');

        // Filter by month/year/status
        if ($status = request('status_stunting')) {
            $query->where('status_stunting', $status);
        }
        if ($bulan = request('bulan')) {
            $query->whereMonth('tanggal_pemeriksaan', $bulan);
        }
        if ($tahun = request('tahun')) {
            $query->whereYear('tanggal_pemeriksaan', $tahun);
        }

        // Search by balita name
        if ($search = request('search')) {
            $query->whereHas('balita', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            });
        }

        $pemeriksaans = $query->paginate(20)->withQueryString();

        return view('posyandu.pemeriksaan.index', compact('pemeriksaans'));
    }

    /**
     * Show the form for creating a new pemeriksaan.
     */
    public function create()
    {
        $balitas = Balita::orderBy('nama')->get();

        return view('posyandu.pemeriksaan.create', compact('balitas'));
    }

    /**
     * Store a newly created pemeriksaan in storage.
     * Automatically classifies stunting status via DecisionTreeService.
     */
    public function store(PemeriksaanRequest $request, DecisionTreeService $dtService)
    {
        $balita = Balita::findOrFail($request->balita_id);
        $umurBulan = $balita->umur_bulan;

        // Run Decision Tree classification
        $hasil = $dtService->classify(
            umurBulan: $umurBulan,
            jenisKelamin: $balita->jenis_kelamin,
            tinggiBadan: (float) $request->tinggi_badan,
            beratBadan: (float) $request->berat_badan,
        );

        Pemeriksaan::create([
            ...$request->validated(),
            'posyandu_id'        => auth()->user()->posyandu_id,
            'umur_bulan'         => $umurBulan,
            'status_stunting'    => $hasil['status'],
            'zscore_tb_u'        => $hasil['zscore'],
            'status_berat_badan' => $hasil['status_berat'],
            'zscore_bb_u'        => $hasil['zscore_bb_u'],
        ]);

        return redirect()->route('posyandu.pemeriksaan.index')
            ->with('success', "Pemeriksaan berhasil disimpan. Status Stunting: {$hasil['status_label']}, Gizi BB: {$hasil['status_berat']}");
    }

    /**
     * Show the form for editing the specified pemeriksaan.
     */
    public function edit(Pemeriksaan $pemeriksaan)
    {
        $balitas = Balita::orderBy('nama')->get();

        return view('posyandu.pemeriksaan.edit', compact('pemeriksaan', 'balitas'));
    }

    /**
     * Update the specified pemeriksaan in storage.
     * Re-runs Decision Tree classification.
     */
    public function update(PemeriksaanRequest $request, Pemeriksaan $pemeriksaan, DecisionTreeService $dtService)
    {
        $balita = Balita::findOrFail($request->balita_id);
        $umurBulan = $balita->umur_bulan;

        // Re-run Decision Tree classification
        $hasil = $dtService->classify(
            umurBulan: $umurBulan,
            jenisKelamin: $balita->jenis_kelamin,
            tinggiBadan: (float) $request->tinggi_badan,
            beratBadan: (float) $request->berat_badan,
        );

        $pemeriksaan->update([
            ...$request->validated(),
            'umur_bulan'         => $umurBulan,
            'status_stunting'    => $hasil['status'],
            'zscore_tb_u'        => $hasil['zscore'],
            'status_berat_badan' => $hasil['status_berat'],
            'zscore_bb_u'        => $hasil['zscore_bb_u'],
        ]);

        return redirect()->route('posyandu.pemeriksaan.index')
            ->with('success', "Pemeriksaan berhasil diperbarui. Status Stunting: {$hasil['status_label']}, Gizi BB: {$hasil['status_berat']}");
    }

    /**
     * Remove the specified pemeriksaan from storage.
     */
    public function destroy(Pemeriksaan $pemeriksaan)
    {
        $pemeriksaan->delete();

        return redirect()->route('posyandu.pemeriksaan.index')
            ->with('success', 'Data pemeriksaan berhasil dihapus.');
    }
}
