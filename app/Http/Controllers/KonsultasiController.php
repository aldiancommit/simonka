<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKonsultasiRequest;
use App\Http\Requests\UpdateKonsultasiRequest;
use App\Models\Konsultasi;
use Illuminate\Http\Request;

class KonsultasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Konsultasi::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_pemohon', 'like', "%{$search}%")
                ->orWhere('instansi', 'like', "%{$search}%")
                ->orWhere('perihal', 'like', "%{$search}%");
        }

        $konsultasis = $query->latest()->paginate(10);

        return view('konsultasi.index', compact('konsultasis'));
    }

    public function create()
    {
        return view('konsultasi.create');
    }

    public function store(StoreKonsultasiRequest $request)
    {
        Konsultasi::create($request->validated());

        return redirect()->route('konsultasi.index')->with('success', 'Konsultasi berhasil ditambahkan.');
    }

    public function show(Konsultasi $konsultasi)
    {
        return view('konsultasi.show', compact('konsultasi'));
    }

    public function edit(Konsultasi $konsultasi)
    {
        return view('konsultasi.edit', compact('konsultasi'));
    }

    public function update(UpdateKonsultasiRequest $request, Konsultasi $konsultasi)
    {
        $konsultasi->update($request->validated());

        return redirect()->route('konsultasi.index')->with('success', 'Konsultasi berhasil diperbarui.');
    }

    public function destroy(Konsultasi $konsultasi)
    {
        $konsultasi->delete();

        return redirect()->route('konsultasi.index')->with('success', 'Konsultasi berhasil dihapus.');
    }
}
