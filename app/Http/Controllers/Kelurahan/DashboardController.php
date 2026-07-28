<?php

namespace App\Http\Controllers\Kelurahan;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\Pemeriksaan;
use App\Models\Posyandu;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the kelurahan dashboard with analytics.
     */
    public function index()
    {
        // Summary cards
        $totalBalita = Balita::count();
        $totalPosyandu = Posyandu::count();
        $pemeriksaanBulanIni = Pemeriksaan::whereMonth('tanggal_pemeriksaan', now()->month)
            ->whereYear('tanggal_pemeriksaan', now()->year)
            ->count();

        // Get latest pemeriksaan per balita for current status
        $latestPemeriksaans = Pemeriksaan::whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('pemeriksaans')
                ->groupBy('balita_id');
        })->get();

        $statusCounts = [
            'normal'   => $latestPemeriksaans->where('status_stunting', 'Normal')->count(),
            'risk'     => $latestPemeriksaans->where('status_stunting', 'Risk of Stunting')->count(),
            'stunting' => $latestPemeriksaans->where('status_stunting', 'Stunting')->count(),
        ];

        $totalStunting = $statusCounts['risk'] + $statusCounts['stunting'];
        $persenStunting = $totalBalita > 0 ? round(($totalStunting / $totalBalita) * 100, 1) : 0;

        // Chart: Stunting per Posyandu (horizontal bar)
        $stuntingPerPosyandu = Posyandu::select('posyandus.id', 'posyandus.nama')
            ->withCount([
                'pemeriksaans as total_stunting' => function ($q) {
                    $q->whereIn('status_stunting', ['Risk of Stunting', 'Stunting'])
                      ->whereIn('id', function ($sub) {
                          $sub->selectRaw('MAX(id)')
                              ->from('pemeriksaans')
                              ->groupBy('balita_id');
                      });
                },
            ])
            ->orderBy('nama')
            ->get();

        // Chart: Trend 12 bulan terakhir (line chart)
        $trendData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            $label = $date->translatedFormat('M Y');

            $monthPemeriksaans = Pemeriksaan::whereYear('tanggal_pemeriksaan', $date->year)
                ->whereMonth('tanggal_pemeriksaan', $date->month)
                ->get();

            $trendData[] = [
                'label'    => $label,
                'total'    => $monthPemeriksaans->count(),
                'normal'   => $monthPemeriksaans->where('status_stunting', 'Normal')->count(),
                'risk'     => $monthPemeriksaans->where('status_stunting', 'Risk of Stunting')->count(),
                'stunting' => $monthPemeriksaans->where('status_stunting', 'Stunting')->count(),
            ];
        }

        // Ranking posyandu by stunting prevalence
        $rankingPosyandu = Posyandu::withCount(['balitas', 'pemeriksaans as stunting_count' => function ($q) {
            $q->whereIn('status_stunting', ['Risk of Stunting', 'Stunting'])
              ->whereIn('id', function ($sub) {
                  $sub->selectRaw('MAX(id)')
                      ->from('pemeriksaans')
                      ->groupBy('balita_id');
              });
        }])
        ->get()
        ->sortByDesc('stunting_count')
        ->values();

        return view('kelurahan.dashboard', compact(
            'totalBalita',
            'totalPosyandu',
            'pemeriksaanBulanIni',
            'statusCounts',
            'persenStunting',
            'stuntingPerPosyandu',
            'trendData',
            'rankingPosyandu'
        ));
    }
}
