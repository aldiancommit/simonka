@extends('layouts.admin')

@section('title', 'Detail Jadwal Kegiatan')
@section('banner_title', 'Detail Jadwal Kegiatan')
@section('banner_subtitle', 'Informasi lengkap agenda pimpinan #' . $jadwalKegiatan->id)


@section('content')
<div class="glass-card p-4 p-md-5 mb-4 simonka-fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom border-white gap-3">
        <div>
            <h3 class="fw-bolder mb-1 text-dark tracking-tight">{{ $jadwalKegiatan->nama_kegiatan }}</h3>
            <p class="text-muted small mb-0 fw-medium">
                <i class="fas fa-map-marker-alt text-primary-glass me-1"></i>{{ $jadwalKegiatan->lokasi ?: 'Lokasi belum ditentukan' }}
            </p>
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
        <span class="badge {{ $badge }} px-3 py-1.5 fs-6">{{ $jadwalKegiatan->status }}</span>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Tanggal Kegiatan</label>
            <div class="fw-bold text-dark fs-6">{{ $jadwalKegiatan->tanggal->format('d F Y') }}</div>
        </div>
        <div class="col-md-6">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Waktu Pelaksanaan</label>
            <div class="fw-bold text-dark fs-6">
                {{ date('H:i', strtotime($jadwalKegiatan->waktu_mulai)) }} WIB
                @if($jadwalKegiatan->waktu_selesai)
                    - {{ date('H:i', strtotime($jadwalKegiatan->waktu_selesai)) }} WIB
                @endif
            </div>
        </div>
        <div class="col-md-12">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Keterangan & Catatan Kelengkapan</label>
            <div class="glass-card-subtle p-3 rounded-3 text-secondary fw-medium">
                {{ $jadwalKegiatan->keterangan ?: 'Tidak ada keterangan tambahan.' }}
            </div>
        </div>
    </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('agenda.jadwal.edit', ['jadwal' => $jadwalKegiatan]) }}" class="btn btn-fundflow-primary" wire:navigate>
           Edit Jadwal
        </a>
        <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-fundflow-glass" wire:navigate>
            Kembali
        </a>
    </div>
</div>
@endsection
