<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->get('jenis', 'konsultasi');
        $mulai = $request->get('mulai', now()->startOfMonth()->format('Y-m-d'));
        $sampai = $request->get('sampai', now()->endOfMonth()->format('Y-m-d'));
        $status = $request->get('status', 'Semua');

        $data = collect();

        if ($request->has('filter') && $request->get('filter') == '1') {
            if ($jenis === 'jadwal') {
                $query = JadwalKegiatan::whereBetween('tanggal', [$mulai, $sampai]);
                if ($status !== 'Semua') {
                    $query->where('status', $status);
                }
                $data = $query->orderBy('tanggal')->get();
            } else {
                $query = Konsultasi::whereBetween('tanggal_konsultasi', [$mulai, $sampai]);
                if ($status !== 'Semua') {
                    $query->where('status', $status);
                }
                $data = $query->orderBy('tanggal_konsultasi')->get();
            }
        }

        return view('laporan.index', compact('jenis', 'mulai', 'sampai', 'status', 'data'));
    }
}
