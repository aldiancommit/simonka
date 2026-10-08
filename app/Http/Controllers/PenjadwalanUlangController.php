<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenjadwalanUlangRequest;
use App\Http\Requests\UpdatePenjadwalanUlangRequest;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PenjadwalanUlangController extends Controller
{
    public function index(Request $request)
    {
        $query = PenjadwalanUlang::with('konsultasi');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->whereHas('konsultasi', function ($query) use ($search) {
                    $query->where('nama_pemohon', 'like', "%{$search}%")
                        ->orWhere('perihal', 'like', "%{$search}%");
                })->orWhere('alasan', 'like', "%{$search}%");
            });
        }

        $penjadwalanUlangs = $query->latest()->paginate(10);

        return view('penjadwalan-ulang.index', compact('penjadwalanUlangs'));
    }

    public function create()
    {
        $konsultasis = Konsultasi::where('status', 'Disetujui')->latest()->get();

        return view('penjadwalan-ulang.create', compact('konsultasis'));
    }

    public function store(StorePenjadwalanUlangRequest $request)
    {
        $data = $request->validated();
        $konsultasi = Konsultasi::findOrFail($data['konsultasi_id']);
        $data['tanggal_lama'] = $konsultasi->tanggal_konsultasi;
        $data['status'] = $data['status'] ?? 'Menunggu';

        PenjadwalanUlang::create($data);

        return redirect()->route('penjadwalan-ulang.index')->with('success', 'Pengajuan penjadwalan ulang berhasil disimpan.');
    }

    public function show(PenjadwalanUlang $penjadwalanUlang)
    {
        $penjadwalanUlang->load('konsultasi');

        return view('penjadwalan-ulang.show', compact('penjadwalanUlang'));
    }

    public function edit(PenjadwalanUlang $penjadwalanUlang)
    {
        $konsultasis = Konsultasi::where('status', 'Disetujui')
            ->orWhere('id', $penjadwalanUlang->konsultasi_id)
            ->latest()
            ->get();

        return view('penjadwalan-ulang.edit', compact('penjadwalanUlang', 'konsultasis'));
    }

    public function update(UpdatePenjadwalanUlangRequest $request, PenjadwalanUlang $penjadwalanUlang)
    {
        $data = $request->validated();

        DB::transaction(function () use ($penjadwalanUlang, $data): void {
            /** @var Konsultasi $konsultasi */
            $konsultasi = Konsultasi::where('id', $penjadwalanUlang->konsultasi_id)->lockForUpdate()->firstOrFail();

            $oldStatus = $penjadwalanUlang->status;
            $newStatus = $data['status'] ?? $oldStatus;

            if ($newStatus === 'Disetujui') {
                if ($konsultasi->status !== 'Disetujui') {
                    throw ValidationException::withMessages([
                        'status' => 'Penjadwalan ulang tidak dapat disetujui karena permohonan konsultasi tidak berstatus Disetujui.',
                    ]);
                }

                if ($oldStatus !== 'Disetujui' || empty($penjadwalanUlang->snapshot_tanggal_lama)) {
                    $data['snapshot_tanggal_lama'] = $konsultasi->tanggal_konsultasi;
                    $data['snapshot_waktu_mulai_lama'] = $konsultasi->waktu_mulai;
                    $data['snapshot_waktu_selesai_lama'] = $konsultasi->waktu_selesai;
                }

                $newWaktuSelesai = $data['waktu_selesai_baru'] ?? $konsultasi->waktu_selesai;

                $konsultasi->update([
                    'tanggal_konsultasi' => $data['tanggal_baru'] ?? $penjadwalanUlang->tanggal_baru,
                    'waktu_mulai' => $data['waktu_mulai_baru'] ?? $penjadwalanUlang->waktu_mulai_baru,
                    'waktu_selesai' => $newWaktuSelesai,
                ]);

                PenjadwalanUlang::where('konsultasi_id', $konsultasi->id)
                    ->where('id', '!=', $penjadwalanUlang->id)
                    ->where('status', 'Menunggu')
                    ->update(['status' => 'Ditolak']);
            } elseif ($oldStatus === 'Disetujui' && $newStatus !== 'Disetujui') {
                $revertDate = $penjadwalanUlang->snapshot_tanggal_lama ?? $penjadwalanUlang->tanggal_lama;
                $revertStart = $penjadwalanUlang->snapshot_waktu_mulai_lama ?? $konsultasi->waktu_mulai;
                $revertEnd = $penjadwalanUlang->snapshot_waktu_selesai_lama ?? $konsultasi->waktu_selesai;

                $konsultasi->update([
                    'tanggal_konsultasi' => $revertDate,
                    'waktu_mulai' => $revertStart,
                    'waktu_selesai' => $revertEnd,
                ]);
            }

            $penjadwalanUlang->update($data);
        });

        return redirect()->route('penjadwalan-ulang.index')->with('success', 'Penjadwalan ulang berhasil diperbarui.');
    }

    public function destroy(PenjadwalanUlang $penjadwalanUlang)
    {
        $penjadwalanUlang->delete();

        return redirect()->route('penjadwalan-ulang.index')->with('success', 'Penjadwalan ulang berhasil dihapus.');
    }
}
