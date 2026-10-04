<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik Atas
        $konsultasiHariIni = Konsultasi::whereDate('tanggal_konsultasi', today())->count();
        $menungguAcc = Konsultasi::where('status', 'Menunggu')->count();
        $selesaiBulanIni = Konsultasi::where('status', 'Selesai')->whereMonth('tanggal_konsultasi', now()->month)->count();
        $agendaMendatang = JadwalKegiatan::whereDate('tanggal', '>=', today())->count();

        // Data Tabel 1: Agenda Terdekat
        $agendaTerdekat = JadwalKegiatan::whereDate('tanggal', '>=', today())
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->take(5)
            ->get();

        // Data Tabel 2: Konsultasi Terbaru
        $konsultasiTerbaru = Konsultasi::latest()
            ->take(5)
            ->get();

        // Metrik Status (Progress / Monitoring Data)
        $totalKonsultasi = Konsultasi::count();
        $statDisetujui = Konsultasi::where('status', 'Disetujui')->count();
        $statDitolak = Konsultasi::where('status', 'Ditolak')->count();
        $statDibatalkan = Konsultasi::where('status', 'Dibatalkan')->count();

        // Menunggu Reschedule
        $rescheduleMenunggu = PenjadwalanUlang::with('konsultasi')
            ->where('status', 'Menunggu')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'konsultasiHariIni', 'menungguAcc', 'selesaiBulanIni',
            'agendaMendatang', 'agendaTerdekat', 'konsultasiTerbaru',
            'totalKonsultasi', 'statDisetujui', 'statDitolak', 'statDibatalkan',
            'rescheduleMenunggu'
        ));
    }
}
