@extends('layouts.admin')

@section('title', 'Dashboard')
@section('banner_title', 'Dashboard SIMONKA')
@section('banner_subtitle', 'Sistem Informasi Monitoring Konsultasi dan Agenda Kepala Badan.')

@section('banner_action')
    <div class="d-flex align-items-center gap-2">
        @can('create', App\Models\Konsultasi::class)
        <a href="{{ route('konsultasi.create') }}" class="btn btn-fundflow-primary" wire:navigate>
            <i class="fas fa-plus"></i> Tambah Konsultasi
        </a>
        @endcan
        @can('create', App\Models\JadwalKegiatan::class)
        <a href="{{ route('agenda.jadwal.create') }}" class="btn btn-fundflow-glass" wire:navigate>
            <i class="fas fa-calendar-plus"></i> Jadwal Baru
        </a>
        @endcan
    </div>
@endsection

@section('content')
<div class="simonka-fade-in d-flex flex-column gap-4">

    {{-- HERO CARD: Total Monitoring Overview (FundFlow Total Balance Aesthetic) --}}
    <div class="glass-card p-4 p-md-5 position-relative overflow-hidden">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <span class="text-xs fw-bold text-uppercase tracking-wider text-muted d-block mb-1" style="font-size: 0.72rem; letter-spacing: 0.08em;">
                    Total Monitoring Konsultasi
                </span>
                <div class="d-flex align-items-baseline gap-2">
                    <span class="h1 fw-bolder mb-0 text-dark tracking-tight" style="font-size: 2.85rem; letter-spacing: -0.03em;">
                        {{ $totalKonsultasi }}
                    </span>
                    <span class="text-muted fw-semibold" style="font-size: 0.95rem;">permohonan tercatat</span>
                </div>
            </div>

            <!-- Quick Status Summary Pill -->
            <div class="glass-pill px-4 py-2 d-flex align-items-center gap-3 shadow-xs">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-warning" style="width: 8px; height: 8px;"></span>
                    <span class="small fw-bold text-dark">{{ $menungguAcc }} Menunggu</span>
                </div>
                <span class="text-muted">|</span>
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle bg-primary" style="width: 8px; height: 8px;"></span>
                    <span class="small fw-bold text-dark">{{ $statDisetujui }} Disetujui</span>
                </div>
            </div>
        </div>

        <!-- Connected Glowing Circles Area (FundFlow Signature Element) -->
        <div class="d-flex flex-column flex-lg-row align-items-center justify-content-between gap-4 mt-2 pt-2">
            
            <!-- 3 Linked Bubbles -->
            <div class="position-relative d-flex align-items-center justify-content-center w-100 py-3" style="max-width: 480px;">
                <!-- Background Connection Glow -->
                <div class="position-absolute w-75 h-50 bg-primary opacity-25 rounded-pill filter-blur" style="filter: blur(25px);"></div>

                <!-- Left Circle: Menunggu ACC -->
                <div class="glass-pill d-flex flex-column align-items-center justify-content-center text-center shadow-md position-relative card-hover-lift" 
                     style="width: 125px; height: 125px; border-radius: 50% !important; margin-right: -20px; z-index: 2;">
                    <span class="h4 fw-bold text-dark mb-0 lh-1">{{ $menungguAcc }}</span>
                    <span class="text-muted fw-semibold mt-1" style="font-size: 0.72rem;">Menunggu</span>
                </div>

                <!-- Center Highlight Circle: Disetujui (Glowing Indigo/Blue Gradient) -->
                <div class="card-gradient-feature d-flex flex-column align-items-center justify-content-center text-center shadow-lg position-relative card-hover-lift" 
                     style="width: 145px; height: 145px; border-radius: 50% !important; z-index: 3; transform: scale(1.05);">
                    <span class="h3 fw-bolder text-white mb-0 lh-1">{{ $statDisetujui }}</span>
                    <span class="text-white text-opacity-90 fw-semibold mt-1" style="font-size: 0.76rem;">Disetujui</span>
                </div>

                <!-- Right Circle: Selesai Bulan Ini -->
                <div class="glass-pill d-flex flex-column align-items-center justify-content-center text-center shadow-md position-relative card-hover-lift" 
                     style="width: 125px; height: 125px; border-radius: 50% !important; margin-left: -20px; z-index: 2;">
                    <span class="h4 fw-bold text-dark mb-0 lh-1">{{ $selesaiBulanIni }}</span>
                    <span class="text-muted fw-semibold mt-1" style="font-size: 0.72rem;">Selesai</span>
                </div>
            </div>

            <!-- Quick Action Shortcuts -->
            <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 w-100" style="max-width: 220px;">
                @can('create', App\Models\Konsultasi::class)
                <a href="{{ route('konsultasi.create') }}" class="btn btn-fundflow-primary w-100 py-2.5 text-center" wire:navigate>
                    <i class="fas fa-plus-circle me-1"></i> Ajukan Konsultasi
                </a>
                @endcan
                @can('create', App\Models\JadwalKegiatan::class)
                <a href="{{ route('agenda.jadwal.create') }}" class="btn btn-fundflow-glass w-100 py-2.5 text-center" wire:navigate>
                    <i class="fas fa-calendar-plus me-1"></i> Jadwalkan Agenda
                </a>
                @endcan
                <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-fundflow-glass w-100 py-2 text-center text-muted" wire:navigate style="font-size: 0.78rem;">
                    <i class="fas fa-clock me-1"></i> Review Reschedule
                </a>
            </div>
        </div>
    </div>

    {{-- SECONDARY METRIC CARDS ROW --}}
    <div class="row g-4">
        {{-- Metric 1: Konsultasi Hari Ini --}}
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card p-4 h-100 card-hover-lift">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Hari Ini</span>
                    <div class="glass-pill p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="h2 fw-bolder text-dark mb-1">{{ $konsultasiHariIni }}</div>
                <p class="text-muted small mb-0 fw-medium">Sesi konsultasi terjadwal hari ini</p>
            </div>
        </div>

        {{-- Metric 2: Menunggu Persetujuan --}}
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card p-4 h-100 card-hover-lift simonka-delay-1">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Menunggu ACC</span>
                    <span class="badge bg-soft-warning">Perlu Tindakan</span>
                </div>
                <div class="h2 fw-bolder text-dark mb-1">{{ $menungguAcc }}</div>
                <p class="text-muted small mb-0 fw-medium">Permohonan belum diverifikasi</p>
            </div>
        </div>

        {{-- Metric 3: Financial Health Inspired Gradient Card (Efektivitas Layanan) --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card-gradient-feature p-4 h-100 card-hover-lift simonka-delay-2 position-relative overflow-hidden d-flex flex-col justify-content-between">
                <div>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-white text-opacity-90 fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Tingkat Approval</span>
                        <div class="bg-white bg-opacity-20 rounded-circle p-1.5 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            <i class="fas fa-chart-line text-white" style="font-size: 0.75rem;"></i>
                        </div>
                    </div>
                    @php
                        $completionRate = $totalKonsultasi > 0 ? round((($statDisetujui + $selesaiBulanIni) / $totalKonsultasi) * 100) : 100;
                    @endphp
                    <div class="h2 fw-bolder text-white mb-0">{{ $completionRate }}%</div>
                    <span class="text-white text-opacity-80 small fw-medium">Tingkat persetujuan permohonan</span>
                </div>
                
                <!-- Wave Line SVG Graphic (FundFlow Signature Aesthetic) -->
                <div class="mt-3" style="height: 48px;">
                    <svg class="w-100 h-100" viewBox="0 0 240 60" fill="none">
                        <defs>
                            <linearGradient id="waveGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                <stop offset="0%" stop-color="white" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="white" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>
                        <path d="M 0 50 C 40 50, 50 35, 80 32 C 110 28, 130 52, 160 45 C 190 38, 200 12, 230 10 L 230 60 L 0 60 Z" fill="url(#waveGradient)"/>
                        <path d="M 0 50 C 40 50, 50 35, 80 32 C 110 28, 130 52, 160 45 C 190 38, 200 12, 230 10" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="230" cy="10" r="3" fill="white"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Metric 4: Agenda Terdekat --}}
        <div class="col-sm-6 col-xl-3">
            <div class="glass-card p-4 h-100 card-hover-lift simonka-delay-3">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-muted fw-bold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Agenda Resmi</span>
                    <div class="glass-pill p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                        </svg>
                    </div>
                </div>
                <div class="h2 fw-bolder text-dark mb-1">{{ $agendaMendatang }}</div>
                <p class="text-muted small mb-0 fw-medium">Kegiatan pimpinan mendatang</p>
            </div>
        </div>
    </div>

    {{-- MAIN ASYMMETRIC GRID: 8 COLUMNS (LEFT) + 4 COLUMNS (RIGHT) --}}
    <div class="row g-4 align-items-start">

        {{-- LEFT COLUMN: 8 COLUMNS --}}
        <div class="col-lg-8 d-flex flex-column gap-4">

            {{-- 1. Permohonan Konsultasi Terbaru --}}
            <div class="glass-card p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="fw-bolder mb-1 text-dark tracking-tight">Permohonan Konsultasi Terbaru</h4>
                        <!-- <p class="text-muted small mb-0">Daftar permohonan konsultasi yang baru masuk ke sistem</p> -->
                    </div>
                    <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>
                        Lihat Semua
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Pemohon</th>
                                <!-- <th>Perihal</th> -->
                                <th>Jadwal</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($konsultasiTerbaru as $konsultasi)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $konsultasi->nama_pemohon }}</div>
                                    <span class="text-muted small">{{ $konsultasi->instansi ?: 'Perorangan' }}</span>
                                </td>
                                <!-- <td>
                                    <span class="d-inline-block text-truncate fw-medium text-slate-700" style="max-width: 200px;" title="{{ $konsultasi->perihal }}">
                                        {{ $konsultasi->perihal }}
                                    </span>
                                </td> -->
                                <td>
                                    <div class="fw-bold text-dark">{{ $konsultasi->tanggal_konsultasi->format('d/m/Y') }}</div>
                                    <span class="text-muted small">{{ date('H:i', strtotime($konsultasi->waktu_mulai)) }} WIB</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $badgeClass = match($konsultasi->status) {
                                            'Menunggu' => 'bg-soft-warning',
                                            'Disetujui' => 'bg-soft-info',
                                            'Selesai' => 'bg-soft-success',
                                            'Ditolak' => 'bg-soft-danger',
                                            'Dibatalkan' => 'bg-soft-secondary',
                                            default => 'bg-soft-primary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $konsultasi->status }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('konsultasi.show', $konsultasi) }}" class="btn btn-fundflow-glass btn-sm py-1 px-3" wire:navigate>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="fas fa-inbox fa-lg">!</i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">Belum Ada Permohonan</h6>
                                    <p class="text-muted small mb-0">Permohonan konsultasi baru akan muncul di sini.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. Pengajuan Reschedule Menunggu Review --}}
            <div class="glass-card p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div>
                        <h4 class="fw-bolder mb-1 text-dark tracking-tight">Review Penjadwalan Ulang</h4>
                        <!-- <p class="text-muted small mb-0">Pengajuan perubahan waktu konsultasi yang membutuhkan persetujuan</p> -->
                    </div>
                    <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>
                        Semua Reschedule
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Pemohon</th>
                                <th>Usulan Baru</th>
                                <th>Alasan</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rescheduleMenunggu as $reschedule)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 160px;" title="{{ $reschedule->konsultasi?->nama_pemohon }}">
                                        {{ $reschedule->konsultasi?->nama_pemohon ?? 'Konsultasi' }}
                                    </div>
                                    <span class="text-muted small">{{ $reschedule->konsultasi?->instansi ?: 'Perorangan' }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-primary">{{ $reschedule->tanggal_baru->format('d/m/Y') }}</div>
                                    <span class="text-muted small">{{ date('H:i', strtotime($reschedule->waktu_mulai_baru)) }} WIB</span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate text-muted small" style="max-width: 180px;" title="{{ $reschedule->alasan }}">
                                        {{ $reschedule->alasan }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('penjadwalan-ulang.edit', $reschedule) }}" class="btn btn-fundflow-primary btn-sm py-1 px-3" wire:navigate>
                                        Review
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="empty-state py-4">
                                    <p class="text-muted small mb-0">Tidak ada pengajuan reschedule yang menunggu review.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: 4 COLUMNS --}}
        <div class="col-lg-4 d-flex flex-column gap-4">

            {{-- 1. Agenda Resmi Terdekat (FundFlow Upcoming Payments Style) --}}
            <div class="glass-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <h4 class="fw-bolder mb-0 text-dark tracking-tight">Agenda Terdekat</h4>
                        <!-- <p class="text-muted small mb-0">Jadwal kegiatan pimpinan</p> -->
                    </div>
                    <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>
                        Lihat Semua
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($agendaTerdekat as $agenda)
                        <div class="glass-card-subtle p-3 rounded-3 card-hover-lift d-flex flex-column gap-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-soft-primary">
                                    {{ $agenda->tanggal->format('d M Y') }}
                                </span>
                                @php
                                    $agendaBadge = match($agenda->status) {
                                        'Terjadwal' => 'bg-soft-info',
                                        'Berlangsung' => 'bg-soft-primary',
                                        'Selesai' => 'bg-soft-success',
                                        'Dibatalkan' => 'bg-soft-secondary',
                                        default => 'bg-soft-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $agendaBadge }}">{{ $agenda->status }}</span>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $agenda->nama_kegiatan }}">{{ $agenda->nama_kegiatan }}</h6>
                                <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 0.76rem;">
                                    <span><i class="far fa-clock me-1"></i>{{ date('H:i', strtotime($agenda->waktu_mulai)) }} WIB</span>
                                    @if($agenda->lokasi)
                                        <span class="text-truncate" style="max-width: 140px;"><i class="fas fa-map-marker-alt me-1"></i>{{ $agenda->lokasi }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state py-4">
                            <p class="text-muted small mb-0">Belum ada agenda pimpinan yang terjadwal.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- 2. Status Monitoring Breakdown (FundFlow Quick Status Aesthetic) --}}
            <div class="glass-card p-4">
    <h5 class="fw-bolder mb-4 text-dark tracking-tight">Status Monitoring</h5>
    <!-- <p class="text-muted small mb-3">Distribusi status seluruh permohonan</p> -->

    <!-- Satu wrapper card/box utama untuk semua status -->
    <div class="glass-card-subtle p-3 rounded-3 d-flex flex-column gap-3">
        
        <!-- Menunggu ACC -->
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-warning p-2 rounded-circle">
                    <i class="fas fa-clock fa-xs"></i>
                </span>
                <span class="fw-semibold text-dark small">Menunggu ACC</span>
            </div>
            <span class="fw-bold text-warning">{{ $menungguAcc }}</span>
        </div>

        <!-- Disetujui -->
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-info p-2 rounded-circle">
                    <i class="fas fa-check fa-xs"></i>
                </span>
                <span class="fw-semibold text-dark small">Disetujui</span>
            </div>
            <span class="fw-bold text-info">{{ $statDisetujui }}</span>
        </div>

        <!-- Selesai (Bulan Ini) -->
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-success p-2 rounded-circle">
                    <i class="fas fa-check-double fa-xs"></i>
                </span>
                <span class="fw-semibold text-dark small">Selesai (Bulan Ini)</span>
            </div>
            <span class="fw-bold text-success">{{ $selesaiBulanIni }}</span>
        </div>

        <!-- Ditolak -->
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-soft-danger p-2 rounded-circle">
                    <i class="fas fa-times fa-xs"></i>
                </span>
                <span class="fw-semibold text-dark small">Ditolak</span>
            </div>
            <span class="fw-bold text-danger">{{ $statDitolak }}</span>
        </div>

        <!-- Dibatalkan -->
        <div class="d-flex align-items-center justify-content-between">

        </div>

    </div>

</div>
@endsection
