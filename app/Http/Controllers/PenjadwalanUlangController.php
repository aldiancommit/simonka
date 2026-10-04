<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePenjadwalanUlangRequest;
use App\Http\Requests\UpdatePenjadwalanUlangRequest;
use App\Models\Konsultasi;
use App\Models\PenjadwalanUlang;
use Illuminate\Http\Request;

class PenjadwalanUlangController extends Controller
{
    public function index(Request $request)
    {
        $query = PenjadwalanUlang::with('konsultasi');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('konsultasi', function ($q) use ($search) {
                $q->where('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('perihal', 'like', "%{$search}%");
            })->orWhere('alasan', 'like', "%{$search}%");
        }

        $penjadwalanUlangs = $query->latest()->paginate(10);

        return view('penjadwalan-ulang.index', compact('penjadwalanUlangs'));
    }

    public function create()
    {
        $konsultasis = Konsultasi::latest()->get();

        return view('penjadwalan-ulang.create', compact('konsultasis'));
    }

    public function store(StorePenjadwalanUlangRequest $request)
    {
        PenjadwalanUlang::create($request->validated());

        return redirect()->route('penjadwalan-ulang.index')->with('success', 'Pengajuan penjadwalan ulang berhasil disimpan.');
    }

    public function show(PenjadwalanUlang $penjadwalanUlang)
    {
        return view('penjadwalan-ulang.show', compact('penjadwalanUlang'));
    }

    public function edit(PenjadwalanUlang $penjadwalanUlang)
    {
        $konsultasis = Konsultasi::latest()->get();

        return view('penjadwalan-ulang.edit', compact('penjadwalanUlang', 'konsultasis'));
    }

    public function update(UpdatePenjadwalanUlangRequest $request, PenjadwalanUlang $penjadwalanUlang)
    {
        $penjadwalanUlang->update($request->validated());

        if ($penjadwalanUlang->status === 'Disetujui') {
            $penjadwalanUlang->konsultasi()->update([
                'tanggal_konsultasi' => $penjadwalanUlang->tanggal_baru,
                'waktu_mulai' => $penjadwalanUlang->waktu_mulai_baru,
                'waktu_selesai' => $penjadwalanUlang->waktu_selesai_baru,
            ]);
        }

        return redirect()->route('penjadwalan-ulang.index')->with('success', 'Penjadwalan ulang berhasil diperbarui.');
    }

    public function destroy(PenjadwalanUlang $penjadwalanUlang)
    {
        $penjadwalanUlang->delete();

        return redirect()->route('penjadwalan-ulang.index')->with('success', 'Penjadwalan ulang berhasil dihapus.');
    }
}
