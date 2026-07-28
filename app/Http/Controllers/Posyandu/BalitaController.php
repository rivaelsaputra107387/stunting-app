<?php

namespace App\Http\Controllers\Posyandu;

use App\Http\Controllers\Controller;
use App\Http\Requests\BalitaRequest;
use App\Models\Balita;

class BalitaController extends Controller
{
    /**
     * Display a listing of balitas.
     */
    public function index()
    {
        $query = Balita::with('latestPemeriksaan')->latest();

        // Search by nama or NIK
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $balitas = $query->paginate(15)->withQueryString();

        return view('posyandu.balita.index', compact('balitas'));
    }

    /**
     * Show the form for creating a new balita.
     */
    public function create()
    {
        return view('posyandu.balita.create');
    }

    /**
     * Store a newly created balita in storage.
     */
    public function store(BalitaRequest $request)
    {
        Balita::create([
            ...$request->validated(),
            'posyandu_id' => auth()->user()->posyandu_id,
        ]);

        return redirect()->route('posyandu.balita.index')
            ->with('success', 'Data balita berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified balita.
     */
    public function edit(Balita $balitum)
    {
        return view('posyandu.balita.edit', ['balita' => $balitum]);
    }

    /**
     * Update the specified balita in storage.
     */
    public function update(BalitaRequest $request, Balita $balitum)
    {
        $balitum->update($request->validated());

        return redirect()->route('posyandu.balita.index')
            ->with('success', 'Data balita berhasil diperbarui.');
    }

    /**
     * Remove the specified balita from storage.
     */
    public function destroy(Balita $balitum)
    {
        $balitum->delete();

        return redirect()->route('posyandu.balita.index')
            ->with('success', 'Data balita berhasil dihapus.');
    }
}
