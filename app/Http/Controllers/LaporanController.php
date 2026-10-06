<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $jenis = $request->input('jenis', 'konsultasi');
        $statuses = $jenis === 'jadwal'
            ? ['Semua', 'Terjadwal', 'Berlangsung', 'Selesai', 'Dibatalkan']
            : ['Semua', 'Menunggu', 'Disetujui', 'Selesai', 'Ditolak', 'Dibatalkan'];

        $filters = $request->validate([
            'filter' => ['sometimes', 'in:0,1'],
            'jenis' => ['sometimes', Rule::in(['konsultasi', 'jadwal'])],
            'mulai' => ['required_if:filter,1', 'date_format:Y-m-d'],
            'sampai' => ['required_if:filter,1', 'date_format:Y-m-d', 'after_or_equal:mulai'],
            'status' => ['sometimes', Rule::in($statuses)],
        ]);

        $jenis = $filters['jenis'] ?? 'konsultasi';
        $mulai = $filters['mulai'] ?? now()->startOfMonth()->format('Y-m-d');
        $sampai = $filters['sampai'] ?? now()->endOfMonth()->format('Y-m-d');
        $status = $filters['status'] ?? 'Semua';

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
