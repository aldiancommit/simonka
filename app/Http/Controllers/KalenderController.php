<?php

namespace App\Http\Controllers;

use App\Models\JadwalKegiatan;
use App\Models\Konsultasi;

class KalenderController extends Controller
{
    public function index()
    {
        $jadwal = JadwalKegiatan::all()->map(function ($item) {
            $color = match ($item->status) {
                'Terjadwal' => '#0dcaf0', // info
                'Berlangsung' => '#0d6efd', // primary
                'Selesai' => '#198754', // success
                'Dibatalkan' => '#6c757d', // secondary
                default => '#0dcaf0'
            };

            return [
                'title' => 'Jadwal: '.$item->nama_kegiatan,
                'start' => $item->tanggal->format('Y-m-d').'T'.$item->waktu_mulai,
                'end' => $item->waktu_selesai ? $item->tanggal->format('Y-m-d').'T'.$item->waktu_selesai : null,
                'color' => $color,
                'url' => route('agenda.jadwal.edit', $item->id),
            ];
        });

        $konsultasi = Konsultasi::whereIn('status', ['Disetujui', 'Selesai'])->get()->map(function ($item) {
            $color = $item->status === 'Selesai' ? '#198754' : '#ffc107'; // success or warning/yellowish

            return [
                'title' => 'Konsultasi: '.$item->nama_pemohon,
                'start' => $item->tanggal_konsultasi->format('Y-m-d').'T'.$item->waktu_mulai,
                'end' => $item->waktu_selesai ? $item->tanggal_konsultasi->format('Y-m-d').'T'.$item->waktu_selesai : null,
                'color' => $color,
                'url' => route('konsultasi.edit', $item->id),
            ];
        });

        $events = $jadwal->concat($konsultasi);

        return view('agenda.kalender.index', compact('events'));
    }
}
