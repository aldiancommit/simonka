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
