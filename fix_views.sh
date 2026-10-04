#!/bin/bash
# 1. Update Konsultasi Index
cat << 'BLADE' > resources/views/konsultasi/index.blade.php
@extends('layouts.admin')
@section('title', 'Data Konsultasi')
@section('banner_title', 'Konsultasi')
@section('banner_subtitle', 'Manajemen daftar konsultasi')
@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
        <div class="header-title">
            <h4 class="card-title mb-0">Daftar Konsultasi</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cari</button>
            </form>
            <a href="{{ route('konsultasi.create') }}" class="btn btn-sm btn-primary text-nowrap" wire:navigate>+ Tambah</a>
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
                        <th class="text-nowrap">Nama Pemohon</th>
                        <th class="text-nowrap">Instansi</th>
                        <th>Perihal</th>
                        <th class="text-nowrap">Tanggal & Waktu</th>
                        <th class="text-nowrap text-center">Status</th>
                        <th class="text-nowrap text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($konsultasis as $index => $konsultasi)
                    <tr>
                        <td class="text-nowrap">{{ $konsultasis->firstItem() + $index }}</td>
                        <td class="text-nowrap fw-semibold">{{ \Illuminate\Support\Str::limit($konsultasi->nama_pemohon, 25) }}</td>
                        <td class="text-nowrap text-muted">{{ \Illuminate\Support\Str::limit($konsultasi->instansi ?: '-', 20) }}</td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $konsultasi->perihal }}">
                                {{ $konsultasi->perihal }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex flex-column">
                                <span>{{ $konsultasi->tanggal_konsultasi->format('d/m/Y') }}</span>
                                <small class="text-muted">{{ date('H:i', strtotime($konsultasi->waktu_mulai)) }} - {{ $konsultasi->waktu_selesai ? date('H:i', strtotime($konsultasi->waktu_selesai)) : 'Selesai' }}</small>
                            </div>
                        </td>
                        <td class="text-nowrap text-center">
                            @php
                                $badge = match($konsultasi->status) {
                                    'Menunggu' => 'warning',
                                    'Disetujui' => 'info',
                                    'Selesai' => 'success',
                                    'Ditolak' => 'danger',
                                    'Dibatalkan' => 'secondary',
                                    default => 'light'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $konsultasi->status }}</span>
                        </td>
                        <td class="text-nowrap text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('konsultasi.edit', $konsultasi) }}" class="btn btn-sm btn-info" wire:navigate>Edit</a>
                                <form action="{{ route('konsultasi.destroy', $konsultasi) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada data konsultasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0 pt-3 pb-3">
        {{ $konsultasis->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
BLADE

# 2. Update Jadwal Kegiatan Index
cat << 'BLADE' > resources/views/agenda/jadwal/index.blade.php
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
BLADE

# 3. Update Penjadwalan Ulang Index
cat << 'BLADE' > resources/views/penjadwalan-ulang/index.blade.php
@extends('layouts.admin')
@section('title', 'Penjadwalan Ulang')
@section('banner_title', 'Penjadwalan Ulang')
@section('banner_subtitle', 'Manajemen daftar reschedule konsultasi')
@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
        <div class="header-title">
            <h4 class="card-title mb-0">Daftar Penjadwalan Ulang</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cari</button>
            </form>
            <a href="{{ route('penjadwalan-ulang.create') }}" class="btn btn-sm btn-primary text-nowrap" wire:navigate>+ Ajukan Reschedule</a>
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
                        <th>Konsultasi (Pemohon/Perihal)</th>
                        <th class="text-nowrap text-center">Jadwal Lama</th>
                        <th class="text-nowrap text-center">Jadwal Baru</th>
                        <th class="text-nowrap text-center">Status</th>
                        <th class="text-nowrap text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjadwalanUlangs as $index => $pu)
                    <tr>
                        <td class="text-nowrap">{{ $penjadwalanUlangs->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-nowrap">{{ \Illuminate\Support\Str::limit($pu->konsultasi->nama_pemohon, 25) }}</div>
                            <div class="text-muted d-inline-block text-truncate" style="max-width: 250px; font-size: 0.85rem;" title="{{ $pu->konsultasi->perihal }}">
                                {{ $pu->konsultasi->perihal }}
                            </div>
                        </td>
                        <td class="text-nowrap text-center">
                            <span class="text-muted"><del>{{ $pu->tanggal_lama->format('d/m/Y') }}</del></span>
                        </td>
                        <td class="text-nowrap text-center">
                            <span class="text-primary fw-medium">{{ $pu->tanggal_baru->format('d/m/Y') }}</span><br>
                            <small class="text-muted">{{ date('H:i', strtotime($pu->waktu_mulai_baru)) }}</small>
                        </td>
                        <td class="text-nowrap text-center">
                            @php
                                $badge = match($pu->status) {
                                    'Menunggu' => 'warning',
                                    'Disetujui' => 'info',
                                    'Ditolak' => 'danger',
                                    default => 'light'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $pu->status }}</span>
                        </td>
                        <td class="text-nowrap text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('penjadwalan-ulang.edit', $pu) }}" class="btn btn-sm btn-info" wire:navigate>Edit</a>
                                <form action="{{ route('penjadwalan-ulang.destroy', $pu) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada pengajuan penjadwalan ulang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0 pt-3 pb-3">
        {{ $penjadwalanUlangs->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
BLADE
