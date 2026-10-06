@extends('layouts.admin')

@section('title', 'Tambah Konsultasi')
@section('banner_title', 'Tambah Konsultasi')
@section('banner_subtitle', 'Formulir pendaftaran permohonan konsultasi dan koordinasi baru.')

@section('banner_action')
    <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass" wire:navigate>
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Form Permohonan Konsultasi</h4>
            <p class="text-muted small mb-0">Lengkapi data di bawah ini secara jelas dan akurat.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('konsultasi.store') }}">
        @csrf
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Pemohon <span class="text-danger">*</span></label>
                <input type="text" name="nama_pemohon" class="form-control @error('nama_pemohon') is-invalid @enderror" value="{{ old('nama_pemohon') }}" placeholder="Contoh: Ir. Hendra Gunawan" required>
                @error('nama_pemohon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Instansi / Unit Kerja</label>
                <input type="text" name="instansi" class="form-control @error('instansi') is-invalid @enderror" value="{{ old('instansi') }}" placeholder="Contoh: Bappeda Prov. Jawa Barat">
                @error('instansi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">No. Telepon / WhatsApp</label>
                <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon') }}" placeholder="Contoh: 081298765432">
                @error('no_telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Alamat Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: pemohon@domain.go.id">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12 mb-3">
                <label class="form-label">Perihal / Topik Pembahasan <span class="text-danger">*</span></label>
                <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror" value="{{ old('perihal') }}" placeholder="Contoh: Koordinasi integrasi program strategis 2026" required>
                @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Konsultasi <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_konsultasi" class="form-control @error('tanggal_konsultasi') is-invalid @enderror" value="{{ old('tanggal_konsultasi', date('Y-m-d')) }}" required>
                @error('tanggal_konsultasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai', '09:00') }}" required>
                @error('waktu_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai') }}">
                @error('waktu_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Catatan Tambahan</label>
                <textarea name="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3" placeholder="Tambahkan catatan khusus, dokumen yang perlu disiapkan, atau konteks pembahasan">{{ old('catatan') }}</textarea>
                @error('catatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top border-white">
            <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass" wire:navigate>Batal</a>
            <button type="submit" class="btn btn-fundflow-primary px-4">
                <i class="fas fa-save me-1"></i> Simpan Permohonan
            </button>
        </div>
    </form>
</div>
@endsection
