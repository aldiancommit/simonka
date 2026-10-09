@extends('layouts.admin')

@section('title', 'Penjadwalan Ulang')
@section('banner_title', 'Penjadwalan Ulang')
@section('banner_subtitle', 'Manajemen daftar pengajuan perubahan waktu dan tanggal konsultasi.')


@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <!-- Header with Search & Quick Filter -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Daftar Penjadwalan Ulang</h4>
            <p class="text-muted small mb-0">Total: {{ $penjadwalanUlangs->total() }} pengajuan perubahan jadwal</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-sm-grow-0">
                <div class="position-relative w-100" style="min-width: 220px;">
                    <i class="fas fa-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                    <input type="text" name="search" class="form-control ps-5 py-2 glass-pill text-dark w-100" placeholder="Cari pemohon / alasan..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-fundflow-glass py-2 px-3">
                    <span class="fw-bold">Cari</span>
                </button>
            </form>
            @can('create', App\Models\PenjadwalanUlang::class)
                <a href="{{ route('penjadwalan-ulang.create') }}" class="btn btn-fundflow-primary py-2 px-3.5 d-inline-flex align-items-center gap-1.5" wire:navigate>
                    <span class="fw-bold">Tambah Reschedule</span>
                </a>
            @endcan
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Konsultasi (Pemohon / Perihal)</th>
                    <th class="text-center">Jadwal Sebelumnya</th>
                    <th class="text-center">Usulan Jadwal Baru</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjadwalanUlangs as $index => $pu)
                <tr>
                    <td class="text-center text-muted fw-bold">{{ $penjadwalanUlangs->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ \Illuminate\Support\Str::limit($pu->konsultasi?->nama_pemohon ?? 'Data Dihapus', 28) }}</div>
                        <span class="text-muted small d-inline-block text-truncate" style="max-width: 260px;" title="{{ $pu->konsultasi?->perihal }}">
                            {{ $pu->konsultasi?->perihal ?? '—' }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="text-muted"><del>{{ $pu->tanggal_lama->format('d/m/Y') }}</del></span>
                    </td>
                    <td class="text-center">
                        <div class="fw-bold text-primary-glass">{{ $pu->tanggal_baru->format('d/m/Y') }}</div>
                        <span class="text-muted small">{{ date('H:i', strtotime($pu->waktu_mulai_baru)) }} WIB</span>
                    </td>
                    <td class="text-center">
                        @php
                            $badge = match($pu->status) {
                                'Menunggu' => 'bg-soft-warning',
                                'Disetujui' => 'bg-soft-info',
                                'Ditolak' => 'bg-soft-danger',
                                default => 'bg-soft-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badge }}">{{ $pu->status }}</span>
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
                                    <a href="{{ route('penjadwalan-ulang.show', $pu) }}" 
                                    class="dropdown-item rounded-2 small py-1.5 px-2 d-flex align-items-center gap-2" 
                                    wire:navigate>
                                        <i class="fas fa-eye text-primary fa-fw"></i>
                                        <span>Detail</span>
                                    </a>
                                </li>

                                <!-- Review / Edit -->
                                @can('update', $pu)
                                <li>
                                    <a href="{{ route('penjadwalan-ulang.edit', $pu) }}" 
                                    class="dropdown-item rounded-2 small py-1.5 px-2 d-flex align-items-center gap-2" 
                                    wire:navigate>
                                        <i class="fas fa-check-circle text-info fa-fw"></i>
                                        <span>Review</span>
                                    </a>
                                </li>
                                @endcan

                                @can('delete', $pu)
                                <li><hr class="dropdown-divider my-1 opacity-25"></li>
                                <!-- Hapus -->
                                <li>
                                    <form action="{{ route('penjadwalan-ulang.destroy', $pu) }}" 
                                        method="POST" 
                                        data-confirm-title="Hapus Penjadwalan Ulang"
                                        data-confirm-message="Apakah Anda yakin ingin menghapus permohonan penjadwalan ulang untuk konsultasi &quot;{{ $pu->konsultasi?->nama_pemohon ?? 'ini' }}&quot;? Tindakan ini tidak dapat dibatalkan dan data akan dihapus permanen."
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
                            <i class="fas fa-calendar-alt fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tidak ada pengajuan reschedule</h6>
                        <p class="text-muted small mb-3">
                            @if(request('search'))
                                Pencarian dengan kata kunci "{{ request('search') }}" tidak menemukan hasil.
                            @else
                                Belum ada pengajuan perubahan jadwal yang tercatat.
                            @endif
                        </p>
                        @if(request('search'))
                            <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>Reset Pencarian</a>
                        @else
                            @can('create', App\Models\PenjadwalanUlang::class)
                                <a href="{{ route('penjadwalan-ulang.create') }}" class="btn btn-fundflow-primary btn-sm" wire:navigate>+ Ajukan Reschedule Baru</a>
                            @endcan
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($penjadwalanUlangs->hasPages())
    <div class="pt-4 mt-2 d-flex justify-content-center">
        {{ $penjadwalanUlangs->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
