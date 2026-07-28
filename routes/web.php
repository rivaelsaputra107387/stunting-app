<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Kelurahan;
use App\Http\Controllers\Posyandu;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect('/login'));

// Authentication
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Kelurahan Routes (Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('kelurahan')
    ->middleware(['auth', 'kelurahan'])
    ->name('kelurahan.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [Kelurahan\DashboardController::class, 'index'])->name('dashboard');

        // Posyandu management
        Route::get('/posyandu', [Kelurahan\PosyanduController::class, 'index'])->name('posyandu.index');
        Route::get('/posyandu/{posyandu}', [Kelurahan\PosyanduController::class, 'show'])->name('posyandu.show');

        // Klasifikasi
        Route::get('/klasifikasi', [Kelurahan\KlasifikasiController::class, 'index'])->name('klasifikasi.index');
        Route::post('/klasifikasi/proses', [Kelurahan\KlasifikasiController::class, 'proses'])->name('klasifikasi.proses');
        Route::get('/klasifikasi/proses', fn() => redirect()->route('kelurahan.klasifikasi.index'));

        // Laporan
        Route::get('/laporan', [Kelurahan\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pdf', [Kelurahan\LaporanController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/excel', [Kelurahan\LaporanController::class, 'exportExcel'])->name('laporan.excel');
    });

/*
|--------------------------------------------------------------------------
| Posyandu Routes (Petugas)
|--------------------------------------------------------------------------
*/
Route::prefix('posyandu')
    ->middleware(['auth', 'posyandu'])
    ->name('posyandu.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [Posyandu\DashboardController::class, 'index'])->name('dashboard');

        // CRUD Balita
        Route::resource('balita', Posyandu\BalitaController::class)->except(['show']);

        // CRUD Pemeriksaan
        Route::resource('pemeriksaan', Posyandu\PemeriksaanController::class)->except(['show']);
    });

// Shared Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
});
