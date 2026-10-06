@extends('layouts.admin')

@section('title', 'Edit Konsultasi')
@section('banner_title', 'Edit Konsultasi')
@section('banner_subtitle', 'Pembaruan data permohonan konsultasi #' . $konsultasi->id)

@section('banner_action')
    <div class="d-flex gap-2">
        <a href="{{ route('konsultasi.show', $konsultasi) }}" class="btn btn-fundflow-glass" wire:navigate>
            <i class="fas fa-eye me-1"></i> Detail
        </a>
        <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass" wire:navigate>
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom border-white gap-2">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Edit Data Konsultasi</h4>
            <p class="text-muted small mb-0">Perbarui data permohonan konsultasi di bawah ini.</p>
        </div>
        @php
            $badgeClass = match($konsultasi->status) {
                'Menunggu' => 'bg-soft-warning',
                'Disetujui' => 'bg-soft-info',
                'Selesai' => 'bg-soft-success',
                'Ditolak' => 'bg-soft-danger',
                'Dibatalkan' => 'bg-soft-secondary',
                default => 'bg-soft-secondary'
            };
        @endphp
        <span class="badge {{ $badgeClass }} px-3 py-1.5">Status: {{ $konsultasi->status }}</span>
    </div>

    <form method="POST" action="{{ route('konsultasi.update', $konsultasi) }}">
        @csrf
        @method('PUT')
        
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Pemohon <span class="text-danger">*</span></label>
                <input type="text" name="nama_pemohon" class="form-control @error('nama_pemohon') is-invalid @enderror" value="{{ old('nama_pemohon', $konsultasi->nama_pemohon) }}" required>
                @error('nama_pemohon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Instansi / Unit Kerja</label>
                <input type="text" name="instansi" class="form-control @error('instansi') is-invalid @enderror" value="{{ old('instansi', $konsultasi->instansi) }}">
                @error('instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">No. Telepon / WhatsApp</label>
                <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon', $konsultasi->no_telepon) }}">
                @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $konsultasi->email) }}">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8 mb-3">
                <label class="form-label">Perihal / Topik Pembahasan <span class="text-danger">*</span></label>
                <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror" value="{{ old('perihal', $konsultasi->perihal) }}" required>
                @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Status Konsultasi <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach(['Menunggu', 'Disetujui', 'Selesai', 'Ditolak', 'Dibatalkan'] as $status)
                        <option value="{{ $status }}" {{ old('status', $konsultasi->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
            <div class="col-md-12 mb-3">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ old('catatan', $konsultasi->catatan) }}</textarea>
                @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top border-white">
            <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass" wire:navigate>Batal</a>
            <button type="submit" class="btn btn-fundflow-primary px-4">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
