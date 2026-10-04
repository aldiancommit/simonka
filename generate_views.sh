#!/bin/bash
# Konsultasi
cat << 'BLADE' > resources/views/konsultasi/index.blade.php
@extends('layouts.admin')
@section('title', 'Data Konsultasi')
@section('banner_title', 'Konsultasi')
@section('banner_subtitle', 'Manajemen daftar konsultasi')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="header-title">
            <h4 class="card-title">Daftar Konsultasi</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cari</button>
            </form>
            <a href="{{ route('konsultasi.create') }}" class="btn btn-sm btn-primary" wire:navigate>+ Tambah</a>
        </div>
    </div>
    <div class="card-body p-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pemohon</th>
                        <th>Instansi</th>
                        <th>Perihal</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($konsultasis as $index => $konsultasi)
                    <tr>
                        <td>{{ $konsultasis->firstItem() + $index }}</td>
                        <td>{{ $konsultasi->nama_pemohon }}</td>
                        <td>{{ $konsultasi->instansi ?: '-' }}</td>
                        <td>{{ $konsultasi->perihal }}</td>
                        <td>{{ $konsultasi->tanggal_konsultasi->format('d/m/Y') }}</td>
                        <td>{{ $konsultasi->waktu_mulai }} - {{ $konsultasi->waktu_selesai ?: 'Selesai' }}</td>
                        <td>
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
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('konsultasi.edit', $konsultasi) }}" class="btn btn-sm btn-info" wire:navigate>Edit</a>
                                <form action="{{ route('konsultasi.destroy', $konsultasi) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-3">Tidak ada data konsultasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $konsultasis->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
BLADE

cat << 'BLADE' > resources/views/konsultasi/create.blade.php
@extends('layouts.admin')
@section('title', 'Tambah Konsultasi')
@section('banner_title', 'Konsultasi')
@section('banner_subtitle', 'Tambah data konsultasi baru')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="header-title">
            <h4 class="card-title">Form Tambah Konsultasi</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('konsultasi.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Pemohon <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pemohon" class="form-control @error('nama_pemohon') is-invalid @enderror" value="{{ old('nama_pemohon') }}" required>
                    @error('nama_pemohon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="instansi" class="form-control @error('instansi') is-invalid @enderror" value="{{ old('instansi') }}">
                    @error('instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No Telepon</label>
                    <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon') }}">
                    @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Perihal <span class="text-danger">*</span></label>
                    <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror" value="{{ old('perihal') }}" required>
                    @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Konsultasi <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_konsultasi" class="form-control @error('tanggal_konsultasi') is-invalid @enderror" value="{{ old('tanggal_konsultasi') }}" required>
                    @error('tanggal_konsultasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai') }}" required>
                    @error('waktu_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai') }}">
                    @error('waktu_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan') }}</textarea>
                    @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('konsultasi.index') }}" class="btn btn-light" wire:navigate>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE

cat << 'BLADE' > resources/views/konsultasi/edit.blade.php
@extends('layouts.admin')
@section('title', 'Edit Konsultasi')
@section('banner_title', 'Konsultasi')
@section('banner_subtitle', 'Edit data konsultasi')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="header-title">
            <h4 class="card-title">Form Edit Konsultasi</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('konsultasi.update', $konsultasi) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Pemohon <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pemohon" class="form-control @error('nama_pemohon') is-invalid @enderror" value="{{ old('nama_pemohon', $konsultasi->nama_pemohon) }}" required>
                    @error('nama_pemohon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Instansi</label>
                    <input type="text" name="instansi" class="form-control @error('instansi') is-invalid @enderror" value="{{ old('instansi', $konsultasi->instansi) }}">
                    @error('instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No Telepon</label>
                    <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon', $konsultasi->no_telepon) }}">
                    @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $konsultasi->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Perihal <span class="text-danger">*</span></label>
                    <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror" value="{{ old('perihal', $konsultasi->perihal) }}" required>
                    @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal Konsultasi <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_konsultasi" class="form-control @error('tanggal_konsultasi') is-invalid @enderror" value="{{ old('tanggal_konsultasi', $konsultasi->tanggal_konsultasi->format('Y-m-d')) }}" required>
                    @error('tanggal_konsultasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai', date('H:i', strtotime($konsultasi->waktu_mulai))) }}" required>
                    @error('waktu_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai', $konsultasi->waktu_selesai ? date('H:i', strtotime($konsultasi->waktu_selesai)) : '') }}">
                    @error('waktu_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach(['Menunggu', 'Disetujui', 'Ditolak', 'Selesai', 'Dibatalkan'] as $status)
                            <option value="{{ $status }}" {{ old('status', $konsultasi->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan', $konsultasi->catatan) }}</textarea>
                    @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('konsultasi.index') }}" class="btn btn-light" wire:navigate>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE

# Jadwal Kegiatan
cat << 'BLADE' > resources/views/agenda/jadwal/index.blade.php
@extends('layouts.admin')
@section('title', 'Jadwal Kegiatan')
@section('banner_title', 'Jadwal Kegiatan Resmi')
@section('banner_subtitle', 'Manajemen daftar jadwal kegiatan pimpinan')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="header-title">
            <h4 class="card-title">Daftar Jadwal Kegiatan</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cari</button>
            </form>
            <a href="{{ route('agenda.jadwal.create') }}" class="btn btn-sm btn-primary" wire:navigate>+ Tambah</a>
        </div>
    </div>
    <div class="card-body p-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwalKegiatans as $index => $jadwal)
                    <tr>
                        <td>{{ $jadwalKegiatans->firstItem() + $index }}</td>
                        <td>{{ $jadwal->nama_kegiatan }}</td>
                        <td>{{ $jadwal->tanggal->format('d/m/Y') }}</td>
                        <td>{{ date('H:i', strtotime($jadwal->waktu_mulai)) }} - {{ $jadwal->waktu_selesai ? date('H:i', strtotime($jadwal->waktu_selesai)) : 'Selesai' }}</td>
                        <td>{{ $jadwal->lokasi ?: '-' }}</td>
                        <td>
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
                        <td>
                            <div class="d-flex gap-1">
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
                        <td colspan="7" class="text-center py-3">Tidak ada jadwal kegiatan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $jadwalKegiatans->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
BLADE

cat << 'BLADE' > resources/views/agenda/jadwal/create.blade.php
@extends('layouts.admin')
@section('title', 'Tambah Jadwal Kegiatan')
@section('banner_title', 'Jadwal Kegiatan')
@section('banner_subtitle', 'Tambah jadwal kegiatan baru')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="header-title">
            <h4 class="card-title">Form Tambah Jadwal</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('agenda.jadwal.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan') }}" required>
                    @error('nama_kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal') }}" required>
                    @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai') }}" required>
                    @error('waktu_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai') }}">
                    @error('waktu_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi') }}">
                    @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-light" wire:navigate>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE

cat << 'BLADE' > resources/views/agenda/jadwal/edit.blade.php
@extends('layouts.admin')
@section('title', 'Edit Jadwal Kegiatan')
@section('banner_title', 'Jadwal Kegiatan')
@section('banner_subtitle', 'Edit data jadwal')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="header-title">
            <h4 class="card-title">Form Edit Jadwal</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('agenda.jadwal.update', $jadwalKegiatan) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan', $jadwalKegiatan->nama_kegiatan) }}" required>
                    @error('nama_kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $jadwalKegiatan->tanggal->format('Y-m-d')) }}" required>
                    @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai', date('H:i', strtotime($jadwalKegiatan->waktu_mulai))) }}" required>
                    @error('waktu_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai', $jadwalKegiatan->waktu_selesai ? date('H:i', strtotime($jadwalKegiatan->waktu_selesai)) : '') }}">
                    @error('waktu_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $jadwalKegiatan->lokasi) }}">
                    @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach(['Terjadwal', 'Berlangsung', 'Selesai', 'Dibatalkan'] as $status)
                            <option value="{{ $status }}" {{ old('status', $jadwalKegiatan->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $jadwalKegiatan->keterangan) }}</textarea>
                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-light" wire:navigate>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE

# Penjadwalan Ulang
cat << 'BLADE' > resources/views/penjadwalan-ulang/index.blade.php
@extends('layouts.admin')
@section('title', 'Penjadwalan Ulang')
@section('banner_title', 'Penjadwalan Ulang')
@section('banner_subtitle', 'Manajemen daftar reschedule konsultasi')
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="header-title">
            <h4 class="card-title">Daftar Penjadwalan Ulang</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cari</button>
            </form>
            <a href="{{ route('penjadwalan-ulang.create') }}" class="btn btn-sm btn-primary" wire:navigate>+ Ajukan Reschedule</a>
        </div>
    </div>
    <div class="card-body p-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Konsultasi (Pemohon/Perihal)</th>
                        <th>Tanggal Lama</th>
                        <th>Tanggal Baru</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjadwalanUlangs as $index => $pu)
                    <tr>
                        <td>{{ $penjadwalanUlangs->firstItem() + $index }}</td>
                        <td>{{ $pu->konsultasi->nama_pemohon }}<br><small class="text-muted">{{ $pu->konsultasi->perihal }}</small></td>
                        <td>{{ $pu->tanggal_lama->format('d/m/Y') }}</td>
                        <td>{{ $pu->tanggal_baru->format('d/m/Y') }}<br><small>{{ date('H:i', strtotime($pu->waktu_mulai_baru)) }}</small></td>
                        <td>{{ \Illuminate\Support\Str::limit($pu->alasan, 50) }}</td>
                        <td>
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
                        <td>
                            <div class="d-flex gap-1">
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
                        <td colspan="7" class="text-center py-3">Tidak ada pengajuan penjadwalan ulang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">
        {{ $penjadwalanUlangs->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
BLADE

cat << 'BLADE' > resources/views/penjadwalan-ulang/create.blade.php
@extends('layouts.admin')
@section('title', 'Ajukan Reschedule')
@section('banner_title', 'Penjadwalan Ulang')
@section('banner_subtitle', 'Ajukan reschedule konsultasi')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="header-title">
            <h4 class="card-title">Form Pengajuan Reschedule</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('penjadwalan-ulang.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Konsultasi <span class="text-danger">*</span></label>
                    <select name="konsultasi_id" class="form-select @error('konsultasi_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Konsultasi --</option>
                        @foreach($konsultasis as $k)
                            <option value="{{ $k->id }}" {{ old('konsultasi_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_pemohon }} - {{ $k->perihal }} ({{ $k->tanggal_konsultasi->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('konsultasi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Lama <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_lama" class="form-control @error('tanggal_lama') is-invalid @enderror" value="{{ old('tanggal_lama') }}" required>
                    @error('tanggal_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Baru <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_baru" class="form-control @error('tanggal_baru') is-invalid @enderror" value="{{ old('tanggal_baru') }}" required>
                    @error('tanggal_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Mulai Baru <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai_baru" class="form-control @error('waktu_mulai_baru') is-invalid @enderror" value="{{ old('waktu_mulai_baru') }}" required>
                    @error('waktu_mulai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Selesai Baru</label>
                    <input type="time" name="waktu_selesai_baru" class="form-control @error('waktu_selesai_baru') is-invalid @enderror" value="{{ old('waktu_selesai_baru') }}">
                    @error('waktu_selesai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Alasan Reschedule <span class="text-danger">*</span></label>
                    <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" required>{{ old('alasan') }}</textarea>
                    @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-light" wire:navigate>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE

cat << 'BLADE' > resources/views/penjadwalan-ulang/edit.blade.php
@extends('layouts.admin')
@section('title', 'Edit Reschedule')
@section('banner_title', 'Penjadwalan Ulang')
@section('banner_subtitle', 'Edit pengajuan reschedule')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="header-title">
            <h4 class="card-title">Form Edit Reschedule</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('penjadwalan-ulang.update', $penjadwalanUlang) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Konsultasi <span class="text-danger">*</span></label>
                    <select name="konsultasi_id" class="form-select @error('konsultasi_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Konsultasi --</option>
                        @foreach($konsultasis as $k)
                            <option value="{{ $k->id }}" {{ old('konsultasi_id', $penjadwalanUlang->konsultasi_id) == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_pemohon }} - {{ $k->perihal }} ({{ $k->tanggal_konsultasi->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('konsultasi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Lama <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_lama" class="form-control @error('tanggal_lama') is-invalid @enderror" value="{{ old('tanggal_lama', $penjadwalanUlang->tanggal_lama->format('Y-m-d')) }}" required>
                    @error('tanggal_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Baru <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_baru" class="form-control @error('tanggal_baru') is-invalid @enderror" value="{{ old('tanggal_baru', $penjadwalanUlang->tanggal_baru->format('Y-m-d')) }}" required>
                    @error('tanggal_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Mulai Baru <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai_baru" class="form-control @error('waktu_mulai_baru') is-invalid @enderror" value="{{ old('waktu_mulai_baru', date('H:i', strtotime($penjadwalanUlang->waktu_mulai_baru))) }}" required>
                    @error('waktu_mulai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Selesai Baru</label>
                    <input type="time" name="waktu_selesai_baru" class="form-control @error('waktu_selesai_baru') is-invalid @enderror" value="{{ old('waktu_selesai_baru', $penjadwalanUlang->waktu_selesai_baru ? date('H:i', strtotime($penjadwalanUlang->waktu_selesai_baru)) : '') }}">
                    @error('waktu_selesai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach(['Menunggu', 'Disetujui', 'Ditolak'] as $status)
                            <option value="{{ $status }}" {{ old('status', $penjadwalanUlang->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Jika diubah ke "Disetujui", data waktu konsultasi asli akan otomatis diperbarui ke tanggal dan waktu yang baru.</small>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Alasan Reschedule <span class="text-danger">*</span></label>
                    <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" required>{{ old('alasan', $penjadwalanUlang->alasan) }}</textarea>
                    @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-light" wire:navigate>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
BLADE

