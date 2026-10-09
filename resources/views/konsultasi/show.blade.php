@extends('layouts.admin')

@section('title', 'Detail Konsultasi')
@section('banner_title', 'Detail Konsultasi')
@section('banner_subtitle', 'Informasi lengkap permohonan konsultasi #' . $konsultasi->id)

@section('content')
<div class="glass-card p-4 p-md-5 mb-4 simonka-fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-3 border-bottom border-white gap-3">
        <div>
            <h3 class="fw-bolder mb-1 text-dark tracking-tight">{{ $konsultasi->nama_pemohon }}</h3>
            <p class="text-muted small mb-0 fw-medium">{{ $konsultasi->instansi ?: 'Perorangan / Umum' }}</p>
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
        <span class="badge {{ $badgeClass }} px-3 py-1.5 fs-6">{{ $konsultasi->status }}</span>
    </div>

    <!-- Perihal Highlight Box -->
    <div>
        <span class="text-muted small fw-bold text-uppercase d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.05em;">Perihal Pembahasan</span>
        <div class="glass-card-subtle p-4 rounded-3 mb-4">
            <p class="mb-0 text-dark fw-bold fs-6">{{ $konsultasi->perihal }}</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Tanggal Konsultasi</label>
            <div class="fw-bold text-dark fs-6">{{ $konsultasi->tanggal_konsultasi->format('d F Y') }}</div>
        </div>
        <div class="col-md-6">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Waktu Pelaksanaan</label>
            <div class="fw-bold text-dark fs-6">
                {{ date('H:i', strtotime($konsultasi->waktu_mulai)) }} WIB
                @if($konsultasi->waktu_selesai)
                    - {{ date('H:i', strtotime($konsultasi->waktu_selesai)) }} WIB
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">No. Telepon / WhatsApp</label>
            <div class="fw-semibold text-dark">{{ $konsultasi->no_telepon ?: 'Tidak dicantumkan' }}</div>
        </div>
        <div class="col-md-6">
            <label class="text-muted small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Alamat Email</label>
            <div class="fw-semibold text-dark">{{ $konsultasi->email ?: 'Tidak dicantumkan' }}</div>
        </div>
        <div class="col-md-12">
            <label class="text-muted text-dark small text-uppercase fw-bold d-block mb-1" style="font-size: 0.72rem;">Catatan Khusus</label>
            <div class="glass-card-subtle p-3 rounded-3 text-secondary fw-medium">
                {{ $konsultasi->catatan ?: 'Tidak ada catatan tambahan.' }}
            </div>
        </div>
    </div>
    

    @if($konsultasi->penjadwalanUlangs->isNotEmpty())
        <h5 class="fw-bold text-dark mt-5 mb-3 d-flex align-items-center gap-2">
            <i class="fas fa-history text-primary-glass"></i> Riwayat Pengajuan Reschedule
        </h5>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Tanggal Lama</th>
                        <th>Usulan Tanggal Baru</th>
                        <th>Alasan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($konsultasi->penjadwalanUlangs as $pu)
                        <tr>
                            <td>{{ $pu->tanggal_lama->format('d/m/Y') }}</td>
                            <td class="fw-bold text-primary-glass">{{ $pu->tanggal_baru->format('d/m/Y') }} ({{ date('H:i', strtotime($pu->waktu_mulai_baru)) }})</td>
                            <td class="text-muted">{{ $pu->alasan }}</td>
                            <td>
                                @php
                                    $puBadge = match($pu->status) {
                                        'Menunggu' => 'bg-soft-warning',
                                        'Disetujui' => 'bg-soft-info',
                                        'Ditolak' => 'bg-soft-danger',
                                        default => 'bg-soft-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $puBadge }}">{{ $pu->status }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
        <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('konsultasi.edit', $konsultasi) }}" class="btn btn-fundflow-primary" wire:navigate>
           Edit Data
        </a>
        <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass" wire:navigate>
          Kembali
        </a>
    </div>
</div>
@endsection
