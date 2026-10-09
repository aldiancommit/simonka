@extends('layouts.admin')

@section('title', 'Jadwal Kegiatan')
@section('banner_title', 'Jadwal Kegiatan Resmi')
@section('banner_subtitle', 'Manajemen daftar jadwal agenda dan kegiatan resmi pimpinan.')

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <!-- Header with Search & Quick Filter -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Daftar Jadwal Kegiatan</h4>
            <p class="text-muted small mb-0">Total: {{ $jadwalKegiatans->total() }} kegiatan pimpinan tercatat</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-sm-grow-0">
                <div class="position-relative w-100" style="min-width: 220px;">
                    <i class="fas fa-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                    <input type="text" name="search" class="form-control ps-5 py-2 glass-pill text-dark w-100" placeholder="Cari kegiatan / lokasi..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-fundflow-glass py-2 px-3">
                    <span class="fw-bold">Cari</span>
                </button>
            </form>
            @can('create', App\Models\JadwalKegiatan::class)
                <a href="{{ route('agenda.jadwal.create') }}" class="btn btn-fundflow-primary py-2 px-3.5 d-inline-flex align-items-center gap-1.5" wire:navigate>
                    <span class="fw-bold">Tambah Jadwal</span>
                </a>
            @endcan
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Nama Kegiatan</th>
                    <th>Tanggal & Waktu</th>
                    <th>Lokasi</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwalKegiatans as $index => $jadwal)
                <tr>
                    <td class="text-center text-muted fw-bold">{{ $jadwalKegiatans->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ \Illuminate\Support\Str::limit($jadwal->nama_kegiatan, 32) }}</div>
                        @if($jadwal->keterangan)
                            <span class="text-muted small text-truncate d-block" style="max-width: 280px;">{{ $jadwal->keterangan }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $jadwal->tanggal->format('d/m/Y') }}</div>
                        <span class="text-muted small">{{ date('H:i', strtotime($jadwal->waktu_mulai)) }} - {{ $jadwal->waktu_selesai ? date('H:i', strtotime($jadwal->waktu_selesai)) : 'Selesai' }}</span>
                    </td>
                    <td>
                        <span class="text-slate-700 fw-medium">
                            <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $jadwal->lokasi ?: '-' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @php
                            $badge = match($jadwal->status) {
                                'Terjadwal' => 'bg-soft-info',
                                'Berlangsung' => 'bg-soft-primary',
                                'Selesai' => 'bg-soft-success',
                                'Dibatalkan' => 'bg-soft-secondary',
                                default => 'bg-soft-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ $jadwal->status }}</span>
                    </td>
                    <td class="text-end">
                        <div class="dropdown dropstart d-inline-block">
                            <button class="btn btn-fundflow-glass py-1.5 px-3 rounded-pill d-inline-flex align-items-center gap-2" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-popper-config='{"strategy": "fixed"}'
                                    aria-expanded="false" 
                                    title="Opsi">
                                <i class="fas fa-ellipsis-v text-muted fs-6"></i>
                                <i class="fas fa-caret-down text-muted fs-6"></i>
                            </button>

                            <ul class="dropdown-menu glass-card shadow-lg border-0 p-2" 
                                style="min-width: 150px; z-index: 9999;">
                                
                                <!-- Detail -->
                                <li>
                                    <a href="{{ route('agenda.jadwal.show', ['jadwal' => $jadwal]) }}" 
                                    class="dropdown-item rounded-2 small py-1.5 px-2 d-flex align-items-center gap-2" 
                                    wire:navigate>
                                        <i class="fas fa-eye text-primary fa-fw"></i>
                                        <span>Detail</span>
                                    </a>
                                </li>

                                <!-- Edit -->
                                @can('update', $jadwal)
                                <li>
                                    <a href="{{ route('agenda.jadwal.edit', ['jadwal' => $jadwal]) }}" 
                                    class="dropdown-item rounded-2 small py-1.5 px-2 d-flex align-items-center gap-2" 
                                    wire:navigate>
                                        <i class="fas fa-edit text-info fa-fw"></i>
                                        <span>Edit</span>
                                    </a>
                                </li>
                                @endcan

                                @can('delete', $jadwal)
                                <li><hr class="dropdown-divider my-1 opacity-25"></li>
                                <!-- Hapus -->
                                <li>
                                    <form action="{{ route('agenda.jadwal.destroy', ['jadwal' => $jadwal]) }}" 
                                        method="POST" 
                                        data-confirm-title="Hapus Jadwal Kegiatan"
                                        data-confirm-message="Apakah Anda yakin ingin menghapus jadwal kegiatan &quot;{{ $jadwal->nama_kegiatan }}&quot;? Tindakan ini tidak dapat dibatalkan dan data akan dihapus permanen."
                                        data-confirm-btn="Ya, Hapus Data"
                                        class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="dropdown-item rounded-2 small py-1.5 px-2 text-danger d-flex align-items-center gap-2">
                                            <i class="fas fa-trash-alt fa-fw"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-comments fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tidak ada jadwal kegiatan</h6>
                        <p class="text-muted small mb-3">
                            @if(request('search'))
                                Pencarian dengan kata kunci "{{ request('search') }}" tidak menemukan hasil.
                            @else
                                Belum ada data agenda kegiatan resmi yang tersimpan.
                            @endif
                        </p>
                        @if(request('search'))
                            <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>Reset Pencarian</a>
                        @else
                            @can('create', App\Models\JadwalKegiatan::class)
                                <a href="{{ route('agenda.jadwal.create') }}" class="btn btn-fundflow-primary py-1 px-2 d-inline-flex align-items-center gap-1.5" wire:navigate>Tambah Jadwal Pertama</a>
                            @endcan
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($jadwalKegiatans->hasPages())
    <div class="pt-4 mt-2 d-flex justify-content-center">
        {{ $jadwalKegiatans->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
