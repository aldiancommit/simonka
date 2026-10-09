<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJadwalKegiatanRequest;
use App\Http\Requests\UpdateJadwalKegiatanRequest;
use App\Models\JadwalKegiatan;
use Illuminate\Http\Request;

class JadwalKegiatanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', JadwalKegiatan::class);

        $query = JadwalKegiatan::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($query) use ($search) {
                $query->where('nama_kegiatan', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        $jadwalKegiatans = $query->latest('tanggal')->paginate(10);

        return view('agenda.jadwal.index', compact('jadwalKegiatans'));
    }

    public function create()
    {
        $this->authorize('create', JadwalKegiatan::class);

        return view('agenda.jadwal.create');
    }

    public function store(StoreJadwalKegiatanRequest $request)
    {
        $this->authorize('create', JadwalKegiatan::class);

        JadwalKegiatan::create($request->validated());

        return redirect()->route('agenda.jadwal.index')->with('success', 'Jadwal Kegiatan berhasil ditambahkan.');
    }

    public function show(JadwalKegiatan $jadwal)
    {
        $this->authorize('view', $jadwal);

        return view('agenda.jadwal.show', ['jadwalKegiatan' => $jadwal]);
    }

    public function edit(JadwalKegiatan $jadwal)
    {
        $this->authorize('update', $jadwal);

        return view('agenda.jadwal.edit', ['jadwalKegiatan' => $jadwal]);
    }

    public function update(UpdateJadwalKegiatanRequest $request, JadwalKegiatan $jadwal)
    {
        $this->authorize('update', $jadwal);

        $jadwal->update($request->validated());

        return redirect()->route('agenda.jadwal.index')->with('success', 'Jadwal Kegiatan berhasil diperbarui.');
    }

    public function destroy(JadwalKegiatan $jadwal)
    {
        $this->authorize('delete', $jadwal);

        $jadwal->delete();

        return redirect()->route('agenda.jadwal.index')->with('success', 'Jadwal Kegiatan berhasil dihapus.');
    }
}
