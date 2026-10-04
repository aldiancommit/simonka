<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalKegiatanController;
use App\Http\Controllers\KalenderController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenjadwalanUlangController;
use App\Http\Controllers\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('konsultasi', KonsultasiController::class);
Route::resource('agenda/jadwal', JadwalKegiatanController::class)->names('agenda.jadwal');
Route::resource('penjadwalan-ulang', PenjadwalanUlangController::class);

Route::prefix('agenda')->name('agenda.')->group(function () {
    Route::get('/', function () {
        return view('agenda.index');
    })->name('index');

    Route::prefix('kalender')->name('kalender.')->group(function () {
        Route::get('/', [KalenderController::class, 'index'])->name('index');
    });
});

Route::prefix('riwayat')->name('riwayat.')->group(function () {
    Route::get('/', [RiwayatController::class, 'index'])->name('index');
});

Route::prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', [LaporanController::class, 'index'])->name('index');
});
