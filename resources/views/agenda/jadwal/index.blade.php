@extends('layouts.admin')
@section('title', 'Jadwal Kegiatan')
@section('banner_title', 'Jadwal Kegiatan Resmi')
@section('banner_subtitle', 'Manajemen daftar jadwal kegiatan pimpinan')
@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
        <div class="header-title">
            <h4 class="card-title mb-0">Daftar Jadwal Kegiatan</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cari</button>
            </form>
            <a href="{{ route('agenda.jadwal.create') }}" class="btn btn-sm btn-primary text-nowrap" wire:navigate>+ Tambah</a>
        </div>
    </div>
    <div class="card-body p-0 mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap" style="width: 50px;">No</th>
                        <th class="text-nowrap">Nama Kegiatan</th>
                        <th class="text-nowrap">Tanggal & Waktu</th>
                        <th>Lokasi</th>
                        <th class="text-nowrap text-center">Status</th>
                        <th class="text-nowrap text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalKegiatans as $index => $jadwal)
                    <tr>
                        <td class="text-nowrap">{{ $jadwalKegiatans->firstItem() + $index }}</td>
                        <td class="fw-semibold">
                            <span class="d-inline-block text-truncate" style="max-width: 250px;" title="{{ $jadwal->nama_kegiatan }}">
                                {{ $jadwal->nama_kegiatan }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex flex-column">
                                <span>{{ $jadwal->tanggal->format('d/m/Y') }}</span>
                                <small class="text-muted">{{ date('H:i', strtotime($jadwal->waktu_mulai)) }} - {{ $jadwal->waktu_selesai ? date('H:i', strtotime($jadwal->waktu_selesai)) : 'Selesai' }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="d-inline-block text-truncate text-muted" style="max-width: 200px;" title="{{ $jadwal->lokasi }}">
                                {{ $jadwal->lokasi ?: '-' }}
                            </span>
                        </td>
                        <td class="text-nowrap text-center">
                            @php
                                $badge = match($jadwal->status) {
                                    'Terjadwal' => 'info',
                                    'Berlangsung' => 'primary',
                                    'Selesai' => 'success',
                                    'Dibatalkan' => 'secondary',
                                    default => 'light'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $jadwal->status }}</span>
                        </td>
                        <td class="text-nowrap text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('agenda.jadwal.edit', $jadwal) }}" class="btn btn-sm btn-info" wire:navigate>Edit</a>
                                <form action="{{ route('agenda.jadwal.destroy', $jadwal) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada jadwal kegiatan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0 pt-3 pb-3">
        {{ $jadwalKegiatans->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
