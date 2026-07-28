<?php

namespace App\Http\Controllers\Posyandu;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\Pemeriksaan;

class DashboardController extends Controller
{
    /**
     * Display the posyandu dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        $posyanduId = $user->posyandu_id;

        $totalBalita = Balita::count();
        $pemeriksaanBulanIni = Pemeriksaan::whereMonth('tanggal_pemeriksaan', now()->month)
            ->whereYear('tanggal_pemeriksaan', now()->year)
            ->count();

        // Count stunting status from latest pemeriksaan per balita
        $balitaIds = Balita::pluck('id');
        $latestPemeriksaans = Pemeriksaan::whereIn('balita_id', $balitaIds)
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                    ->from('pemeriksaans')
                    ->where('posyandu_id', auth()->user()->posyandu_id)
                    ->groupBy('balita_id');
            })
            ->get();

        $statusCounts = [
            'normal'   => $latestPemeriksaans->where('status_stunting', 'Normal')->count(),
            'risk'     => $latestPemeriksaans->where('status_stunting', 'Risk of Stunting')->count(),
            'stunting' => $latestPemeriksaans->where('status_stunting', 'Stunting')->count(),
        ];

        $recentPemeriksaans = Pemeriksaan::with('balita')
            ->latest('tanggal_pemeriksaan')
            ->limit(5)
            ->get();

        return view('posyandu.dashboard', compact(
            'user',
            'totalBalita',
            'pemeriksaanBulanIni',
            'statusCounts',
            'recentPemeriksaans'
        ));
    }
}
