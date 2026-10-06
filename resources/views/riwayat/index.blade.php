@extends('layouts.admin')

@section('title', 'Riwayat & Arsip')
@section('banner_title', 'Riwayat & Arsip Kegiatan')
@section('banner_subtitle', 'Arsip dan histori kegiatan resmi serta konsultasi yang telah selesai, ditolak, atau dibatalkan.')

@section('content')
<div class="row simonka-fade-in">
    <div class="col-12">
        <div class="glass-card p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom border-white">
                <div class="d-flex align-items-center gap-2">
                    <a class="btn {{ $tab == 'konsultasi' ? 'btn-fundflow-primary' : 'btn-fundflow-glass' }} btn-sm py-2 px-3" href="{{ route('riwayat.index', ['tab' => 'konsultasi']) }}" wire:navigate>
                        <i class="fas fa-comments me-1"></i> Arsip Konsultasi
                    </a>
                    <a class="btn {{ $tab == 'jadwal' ? 'btn-fundflow-primary' : 'btn-fundflow-glass' }} btn-sm py-2 px-3" href="{{ route('riwayat.index', ['tab' => 'jadwal']) }}" wire:navigate>
                        <i class="fas fa-calendar-check me-1"></i> Arsip Jadwal Kegiatan
                    </a>
                </div>

                <form method="GET" class="d-flex align-items-center gap-2">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <div class="position-relative" style="min-width: 250px;">
                        <i class="fas fa-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                        <input type="text" name="search" class="form-control ps-5 py-2 glass-pill text-dark" placeholder="Cari arsip..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn btn-fundflow-primary py-2 px-3">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('riwayat.index', ['tab' => $tab]) }}" class="btn btn-fundflow-glass py-2 px-3" title="Reset filter" wire:navigate>
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            @if($tab == 'jadwal')
                                <th>Nama Kegiatan</th>
                                <th class="text-center">Tanggal Pelaksanaan</th>
                                <th>Lokasi</th>
                            @else
                                <th>Pemohon & Instansi</th>
                                <th>Perihal</th>
                                <th class="text-center">Tanggal Konsultasi</th>
                            @endif
                            <th class="text-center">Status Akhir</th>
                            <th class="text-end" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="text-center text-muted fw-bold">{{ $data->firstItem() + $index }}</td>
                            @if($tab == 'jadwal')
                                <td>
                                    <div class="fw-bold text-dark text-truncate" style="max-width: 280px;" title="{{ $item->nama_kegiatan }}">
                                        {{ $item->nama_kegiatan }}
                                    </div>
                                    @if($item->keterangan)
                                        <small class="text-muted d-block text-truncate" style="max-width: 280px;">{{ $item->keterangan }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $item->tanggal->format('d/m/Y') }}</div>
                                    <span class="text-muted small">{{ date('H:i', strtotime($item->waktu_mulai)) }} WIB</span>
                                </td>
                                <td>
                                    <span class="text-slate-700 fw-medium">
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $item->lokasi ?: '-' }}
                                    </span>
                                </td>
                            @else
                                <td>
                                    <div class="fw-bold text-dark">{{ \Illuminate\Support\Str::limit($item->nama_pemohon, 25) }}</div>
                                    <span class="text-muted small">{{ \Illuminate\Support\Str::limit($item->instansi ?: 'Perorangan', 25) }}</span>
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate text-slate-700 fw-medium" style="max-width: 260px;" title="{{ $item->perihal }}">
                                        {{ $item->perihal }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $item->tanggal_konsultasi->format('d/m/Y') }}</div>
                                    <span class="text-muted small">{{ date('H:i', strtotime($item->waktu_mulai)) }} WIB</span>
                                </td>
                            @endif
                            <td class="text-center">
                                @php
                                    $badge = match($item->status) {
                                        'Selesai' => 'bg-soft-success',
                                        'Ditolak' => 'bg-soft-danger',
                                        'Dibatalkan' => 'bg-soft-secondary',
                                        default => 'bg-soft-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $item->status }}</span>
                            </td>
                            <td class="text-end">
                                @if($tab == 'jadwal')
                                    <a href="{{ route('agenda.jadwal.show', ['jadwal' => $item]) }}" class="btn btn-fundflow-glass btn-sm py-1 px-3" wire:navigate>
                                        Detail
                                    </a>
                                @else
                                    <a href="{{ route('konsultasi.show', $item) }}" class="btn btn-fundflow-glass btn-sm py-1 px-3" wire:navigate>
                                        Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $tab == 'jadwal' ? 6 : 6 }}" class="empty-state">
                                <div>
                                <h6 class="fw-bold text-dark mb-1">Tidak ada arsip ditemukan</h6>
                                <p class="text-muted small mb-0">
                                    @if(request('search'))
                                        Pencarian dengan kata kunci "{{ request('search') }}" tidak menemukan arsip.
                                    @else
                                        Belum ada data arsip yang berstatus selesai, ditolak, atau dibatalkan.
                                    @endif
                                </p>
                                </div>

                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
            <div class="pt-4 mt-2 d-flex justify-content-center">
                {{ $data->appends(['tab' => $tab, 'search' => request('search')])->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
