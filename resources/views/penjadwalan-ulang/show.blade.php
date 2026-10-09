@extends('layouts.admin')

@section('title', 'Detail Reschedule')
@section('banner_title', 'Detail Reschedule')
@section('banner_subtitle', 'Informasi lengkap pengajuan perubahan jadwal konsultasi #' . $penjadwalanUlang->id)

@section('content')
<div class="glass-card p-4 p-md-5 mb-4 simonka-fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom border-white gap-3">
        <div>
            <h3 class="fw-bolder mb-1 text-dark tracking-tight">{{ $penjadwalanUlang->konsultasi?->nama_pemohon ?? 'Data Konsultasi' }}</h3>
            <p class="text-muted small mb-0 fw-medium">{{ $penjadwalanUlang->konsultasi?->instansi ?: 'Perorangan / Umum' }}</p>
        </div>
        @php
            $badge = match($penjadwalanUlang->status) {
                'Menunggu' => 'bg-soft-warning',
                'Disetujui' => 'bg-soft-info',
                'Ditolak' => 'bg-soft-danger',
                default => 'bg-soft-secondary'
            };
        @endphp
        <span class="badge {{ $badge }} px-3 py-1.5 fs-6">{{ $penjadwalanUlang->status }}</span>
    </div>

    <!-- Perihal Box -->
    <div>
        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Perihal Konsultasi</span>
        <div class="glass-card-subtle p-4 rounded-3 mb-4">
            <p class="mb-0 text-dark fw-bold fs-6">{{ $penjadwalanUlang->konsultasi?->perihal ?? '—' }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Jadwal Sebelumnya</label>
            <div class="fw-bold text-muted fs-6"><del>{{ $penjadwalanUlang->tanggal_lama->format('d F Y') }}</del></div>
        </div>
        <div class="col-md-6">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Usulan Jadwal Baru</label>
            <div class="fw-bold text-primary fs-6">
                {{ $penjadwalanUlang->tanggal_baru->format('d F Y') }} · {{ date('H:i', strtotime($penjadwalanUlang->waktu_mulai_baru)) }} WIB
            </div>
        </div>
        <div class="col-md-12">
            <label class="text-muted small text-dark text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Alasan Pengajuan</label>
            <div class="glass-card-subtle p-3 rounded-3 text-secondary fw-medium">
                {{ $penjadwalanUlang->alasan }}
            </div>
        </div>
    </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('penjadwalan-ulang.edit', $penjadwalanUlang) }}" class="btn btn-fundflow-primary" wire:navigate>
         Review / Edit
        </a>
        <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-fundflow-glass" wire:navigate>
          Kembali
        </a>
    </div>
</div>
@endsection
