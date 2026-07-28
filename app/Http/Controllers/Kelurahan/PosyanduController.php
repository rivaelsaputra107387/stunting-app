<?php

namespace App\Http\Controllers\Kelurahan;

use App\Http\Controllers\Controller;
use App\Models\Posyandu;
use App\Models\Pemeriksaan;

class PosyanduController extends Controller
{
    /**
     * Display a listing of all posyandus with summary.
     */
    public function index()
    {
        $posyandus = Posyandu::withCount(['balitas', 'pemeriksaans'])
            ->withCount(['pemeriksaans as stunting_count' => function ($q) {
                $q->whereIn('status_stunting', ['Risk of Stunting', 'Stunting'])
                  ->whereIn('id', function ($sub) {
                      $sub->selectRaw('MAX(id)')
                          ->from('pemeriksaans')
                          ->groupBy('balita_id');
                  });
            }])
            ->orderBy('rw')
            ->orderBy('nama')
            ->get();

        return view('kelurahan.posyandu.index', compact('posyandus'));
    }

    /**
     * Display the specified posyandu details.
     */
    public function show(Posyandu $posyandu)
    {
        $posyandu->loadCount(['balitas', 'pemeriksaans']);

        $balitas = $posyandu->balitas()
            ->with('latestPemeriksaan')
            ->orderBy('nama')
            ->paginate(15);

        $recentPemeriksaans = $posyandu->pemeriksaans()
            ->with('balita')
            ->latest('tanggal_pemeriksaan')
            ->limit(10)
            ->get();

        // Status counts for this posyandu
        $latestPemeriksaans = Pemeriksaan::where('posyandu_id', $posyandu->id)
            ->whereIn('id', function ($query) use ($posyandu) {
                $query->selectRaw('MAX(id)')
                    ->from('pemeriksaans')
                    ->where('posyandu_id', $posyandu->id)
                    ->groupBy('balita_id');
            })
            ->get();

        $statusCounts = [
            'normal'   => $latestPemeriksaans->where('status_stunting', 'Normal')->count(),
            'risk'     => $latestPemeriksaans->where('status_stunting', 'Risk of Stunting')->count(),
            'stunting' => $latestPemeriksaans->where('status_stunting', 'Stunting')->count(),
        ];

        return view('kelurahan.posyandu.show', compact(
            'posyandu',
            'balitas',
            'recentPemeriksaans',
            'statusCounts'
        ));
    }
}
