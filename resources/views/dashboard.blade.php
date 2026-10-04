@extends('layouts.admin')

@section('title', 'Dashboard')
@section('banner_title', 'Dashboard SIMONKA')
@section('banner_subtitle', 'Ringkasan informasi dan monitoring data.')

@section('content')
<div class="row">
    {{-- Stat Card: Konsultasi Hari Ini --}}
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow-sm border-0 h-100" data-aos="fade-up" data-aos-delay="200">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-muted fw-semibold font-size-14">Konsultasi Hari Ini</p>
                        <h3 class="mb-0 fw-bold">{{ $konsultasiHariIni }}</h3>
                    </div>
                    <div class="text-primary rounded-circle border border-primary p-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <svg width="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.4" d="M12 2C6.48 2 2 6.48 2 12C2 13.85 2.5 15.55 3.39 17L2 22L7.15 20.65C8.59 21.51 10.24 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" fill="currentColor"/>
                            <path d="M8 12H8.01M12 12H12.01M16 12H16.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Card: Menunggu ACC --}}
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow-sm border-0 h-100" data-aos="fade-up" data-aos-delay="300">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-muted fw-semibold font-size-14">Menunggu ACC</p>
                        <h3 class="mb-0 fw-bold">{{ $menungguAcc }}</h3>
                    </div>
                    <div class="text-warning rounded-circle border border-warning p-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <svg width="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.4" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" fill="currentColor"/>
                            <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Card: Selesai Bulan Ini --}}
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow-sm border-0 h-100" data-aos="fade-up" data-aos-delay="400">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-muted fw-semibold font-size-14">Selesai Bulan Ini</p>
                        <h3 class="mb-0 fw-bold">{{ $selesaiBulanIni }}</h3>
                    </div>
                    <div class="text-success rounded-circle border border-success p-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <svg width="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.4" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" fill="currentColor"/>
                            <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stat Card: Agenda Mendatang --}}
    <div class="col-md-6 col-xl-3 mb-4">
        <div class="card shadow-sm border-0 h-100" data-aos="fade-up" data-aos-delay="500">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-muted fw-semibold font-size-14">Agenda Mendatang</p>
                        <h3 class="mb-0 fw-bold">{{ $agendaMendatang }}</h3>
                    </div>
                    <div class="text-info rounded-circle border border-info p-2 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                        <svg width="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path opacity="0.4" d="M18.5 3H5.5C3.567 3 2 4.567 2 6.5V19.5C2 21.433 3.567 23 5.5 23H18.5C20.433 23 22 21.433 22 19.5V6.5C22 4.567 20.433 3 18.5 3Z" fill="currentColor"/>
                            <path d="M16 2V5M8 2V5M2 8.5H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Konsultasi Terbaru --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4" data-aos="fade-up" data-aos-delay="600">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
                <div class="header-title">
                    <h4 class="card-title mb-0">Konsultasi Terbaru</h4>
                </div>
                <a href="{{ route('konsultasi.index') }}" class="btn btn-sm btn-outline-primary" wire:navigate>Lihat Semua</a>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">Pemohon & Perihal</th>
                                <th class="text-nowrap text-center">Tanggal</th>
                                <th class="text-nowrap text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($konsultasiTerbaru as $konsul)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-nowrap">{{ \Illuminate\Support\Str::limit($konsul->nama_pemohon, 30) }}</div>
                                    <div class="text-muted d-inline-block text-truncate" style="max-width: 300px; font-size: 0.85rem;" title="{{ $konsul->perihal }}">{{ $konsul->perihal }}</div>
                                </td>
                                <td class="text-nowrap text-center">{{ $konsul->tanggal_konsultasi->format('d/m/Y') }}</td>
                                <td class="text-nowrap text-end">
                                    @php
                                        $badge = match($konsul->status) {
                                            'Menunggu' => 'warning',
                                            'Disetujui' => 'info',
                                            'Selesai' => 'success',
                                            'Ditolak' => 'danger',
                                            'Dibatalkan' => 'secondary',
                                            default => 'light'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $konsul->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Belum ada permohonan konsultasi.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Agenda Terdekat --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4" data-aos="fade-up" data-aos-delay="700">
            <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
                <div class="header-title">
                    <h4 class="card-title mb-0">Agenda Terdekat</h4>
                </div>
            </div>
            <div class="card-body p-0 mt-3">
                <ul class="list-group list-group-flush rounded-bottom">
                    @forelse($agendaTerdekat as $agenda)
                        <li class="list-group-item px-4 py-3 d-flex align-items-center justify-content-between">
                            <div class="w-100 overflow-hidden">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="text-primary fw-medium small">{{ $agenda->tanggal->format('d M') }} ({{ date('H:i', strtotime($agenda->waktu_mulai)) }})</span>
                                    @php
                                        $badge = match($agenda->status) {
                                            'Terjadwal' => 'info',
                                            'Berlangsung' => 'primary',
                                            'Selesai' => 'success',
                                            'Dibatalkan' => 'secondary',
                                            default => 'light'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }} p-1" style="font-size: 0.65rem;">{{ $agenda->status }}</span>
                                </div>
                                <h6 class="mb-0 text-truncate" title="{{ $agenda->nama_kegiatan }}">{{ $agenda->nama_kegiatan }}</h6>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item px-4 py-4 text-center text-muted">Belum ada agenda terjadwal.</li>
                    @endforelse
                    @if($agendaTerdekat->isNotEmpty())
                        <li class="list-group-item text-center">
                            <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-sm btn-link text-decoration-none w-100" wire:navigate>Lihat Selengkapnya</a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {{-- Monitoring Data Keseluruhan --}}
    <div class="col-lg-12">
        <div class="card shadow-sm border-0 mb-4" data-aos="fade-up" data-aos-delay="800">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <h4 class="card-title mb-0">Monitoring Data & Aktivitas</h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-5 border-end">
                        <p class="text-muted mb-3">Total Seluruh Konsultasi Berdasarkan Status:</p>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-warning text-white rounded p-1"><i class="fas fa-clock fa-sm"></i></div>
                            <div class="flex-grow-1">Menunggu ACC</div>
                            <div class="fw-bold">{{ $menungguAcc }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-info text-white rounded p-1"><i class="fas fa-check fa-sm"></i></div>
                            <div class="flex-grow-1">Disetujui</div>
                            <div class="fw-bold">{{ $statDisetujui }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-success text-white rounded p-1"><i class="fas fa-check-double fa-sm"></i></div>
                            <div class="flex-grow-1">Selesai</div>
                            <div class="fw-bold">{{ $selesaiBulanIni }} (Bulan ini)</div>
                        </div>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="bg-danger text-white rounded p-1"><i class="fas fa-times fa-sm"></i></div>
                            <div class="flex-grow-1">Ditolak</div>
                            <div class="fw-bold">{{ $statDitolak }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-secondary text-white rounded p-1"><i class="fas fa-ban fa-sm"></i></div>
                            <div class="flex-grow-1">Dibatalkan</div>
                            <div class="fw-bold">{{ $statDibatalkan }}</div>
                        </div>
                    </div>
                    <div class="col-md-7 ps-md-4 mt-4 mt-md-0">
                        <h6 class="mb-3">Pengajuan Reschedule Menunggu ACC</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">Pemohon</th>
                                        <th class="text-nowrap">Usulan Baru</th>
                                        <th>Alasan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rescheduleMenunggu as $reschedule)
                                        <tr>
                                            <td class="text-nowrap">
                                                <div class="fw-semibold text-truncate" style="max-width:120px;" title="{{ $reschedule->konsultasi->nama_pemohon }}">{{ $reschedule->konsultasi->nama_pemohon }}</div>
                                            </td>
                                            <td class="text-nowrap text-primary fw-medium">
                                                {{ $reschedule->tanggal_baru->format('d/m') }} <small>({{ date('H:i', strtotime($reschedule->waktu_mulai_baru)) }})</small>
                                            </td>
                                            <td>
                                                <span class="d-inline-block text-truncate text-muted" style="max-width: 150px;" title="{{ $reschedule->alasan }}">{{ $reschedule->alasan }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('penjadwalan-ulang.edit', $reschedule) }}" class="btn btn-xs btn-outline-info" wire:navigate>Review</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-3">Tidak ada pengajuan reschedule baru.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
