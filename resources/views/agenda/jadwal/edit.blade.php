@extends('layouts.admin')

@section('title', 'Edit Jadwal Kegiatan')
@section('banner_title', 'Edit Jadwal Kegiatan')
@section('banner_subtitle', 'Pembaruan data agenda kegiatan resmi pimpinan.')

@section('banner_action')
    <div class="d-flex gap-2">
        <a href="{{ route('agenda.jadwal.show', ['jadwal' => $jadwalKegiatan]) }}" class="btn btn-fundflow-glass" wire:navigate>
            <i class="fas fa-eye me-1"></i> Detail
        </a>
        <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-fundflow-glass" wire:navigate>
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom border-white gap-2">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Edit Jadwal Kegiatan</h4>
            <p class="text-muted small mb-0">Perbarui rincian agenda di bawah ini.</p>
        </div>
        @php
            $badge = match($jadwalKegiatan->status) {
                'Terjadwal' => 'bg-soft-info',
                'Berlangsung' => 'bg-soft-primary',
                'Selesai' => 'bg-soft-success',
                'Dibatalkan' => 'bg-soft-secondary',
                default => 'bg-soft-secondary'
            };
        @endphp
        <span class="badge {{ $badge }} px-3 py-1.5">Status: {{ $jadwalKegiatan->status }}</span>
    </div>

    <form method="POST" action="{{ route('agenda.jadwal.update', ['jadwal' => $jadwalKegiatan]) }}">
        @csrf
        @method('PUT')
        
        <div class="row mb-4">
            <div class="col-md-12 mb-3">
                <label class="form-label">Nama Kegiatan / Agenda <span class="text-danger">*</span></label>
                <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan', $jadwalKegiatan->nama_kegiatan) }}" required>
                @error('nama_kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Kegiatan <span class="text-danger">*</span></label>
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
            <div class="col-md-8 mb-3">
                <label class="form-label">Lokasi / Ruangan</label>
                <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $jadwalKegiatan->lokasi) }}">
                @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Status Kegiatan <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach(['Terjadwal', 'Berlangsung', 'Selesai', 'Dibatalkan'] as $status)
                        <option value="{{ $status }}" {{ old('status', $jadwalKegiatan->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Keterangan / Dokumen Terkait</label>
                <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $jadwalKegiatan->keterangan) }}</textarea>
                @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top border-white">
            <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-fundflow-glass" wire:navigate>Batal</a>
            <button type="submit" class="btn btn-fundflow-primary px-4">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
