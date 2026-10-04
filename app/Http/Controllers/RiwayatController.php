<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'konsultasi');
        $search = $request->get('search');

        if ($tab === 'jadwal') {
            $query = JadwalKegiatan::whereIn('status', ['Selesai', 'Dibatalkan']);
            if ($search) {
                $query->where('nama_kegiatan', 'like', "%{$search}%");
            }
            $data = $query->latest('tanggal')->paginate(10);
        } else {
            // Default to konsultasi
            $query = Konsultasi::whereIn('status', ['Selesai', 'Ditolak', 'Dibatalkan']);
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_pemohon', 'like', "%{$search}%")
                        ->orWhere('instansi', 'like', "%{$search}%")
                        ->orWhere('perihal', 'like', "%{$search}%");
                });
            }
            $data = $query->latest('tanggal_konsultasi')->paginate(10);
        }

        return view('riwayat.index', compact('data', 'tab'));
    }
}
