<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $konsultasiHariIni = Konsultasi::whereDate('tanggal_konsultasi', today())->count();
        $konsultasiPerStatus = Konsultasi::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $menungguAcc = (int) $konsultasiPerStatus->get('Menunggu', 0);
        $selesaiBulanIni = Konsultasi::where('status', 'Selesai')
            ->whereBetween('tanggal_konsultasi', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->count();

        $agendaMendatangQuery = JadwalKegiatan::whereDate('tanggal', '>=', today())
            ->whereIn('status', ['Terjadwal', 'Berlangsung']);
        $agendaMendatang = (clone $agendaMendatangQuery)->count();
        $agendaTerdekat = $agendaMendatangQuery
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai')
            ->take(5)
            ->get();

        $konsultasiTerbaru = Konsultasi::latest()
            ->take(5)
            ->get();

        $rescheduleMenungguQuery = PenjadwalanUlang::where('status', 'Menunggu');
        $rescheduleMenungguCount = (clone $rescheduleMenungguQuery)->count();
        $rescheduleMenunggu = $rescheduleMenungguQuery
            ->with('konsultasi')
            ->latest()
            ->take(5)
            ->get();

        $statDisetujui = (int) $konsultasiPerStatus->get('Disetujui', 0);
        $statDitolak = (int) $konsultasiPerStatus->get('Ditolak', 0);
        $statDibatalkan = (int) $konsultasiPerStatus->get('Dibatalkan', 0);
        $totalKonsultasi = (int) $konsultasiPerStatus->sum();

        return view('dashboard', compact(
            'konsultasiHariIni', 'menungguAcc', 'selesaiBulanIni', 'rescheduleMenungguCount',
            'agendaMendatang', 'agendaTerdekat', 'konsultasiTerbaru',
            'totalKonsultasi', 'statDisetujui', 'statDitolak', 'statDibatalkan',
            'rescheduleMenunggu'
        ));
    }
}
