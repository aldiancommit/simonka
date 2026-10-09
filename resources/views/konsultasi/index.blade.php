@extends('layouts.admin')

@section('title', 'Data Konsultasi')
@section('banner_title', 'Data Konsultasi')
@section('banner_subtitle', 'Manajemen daftar permohonan konsultasi dan koordinasi.')

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <!-- Header with Search & Quick Filter -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Daftar Konsultasi</h4>
            <p class="text-muted small mb-0">Total: {{ $konsultasis->total() }} data permohonan terdaftar</p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            <form method="GET" class="d-flex align-items-center gap-2 flex-grow-1 flex-sm-grow-0">
                <div class="position-relative w-100" style="min-width: 220px;">
                    <i class="fas fa-search position-absolute text-muted" style="left: 14px; top: 50%; transform: translateY(-50%); font-size: 0.85rem;"></i>
                    <input type="text" name="search" class="form-control ps-5 py-2 glass-pill text-dark w-100" placeholder="Cari pemohon / perihal..." value="{{ request('search') }}">
                </div>
                <button type="submit" class="btn btn-fundflow-glass py-2 px-3">
                    <span class="fw-bold">Cari</span>
                </button>
            </form>
            @can('create', App\Models\Konsultasi::class)
                <a href="{{ route('konsultasi.create') }}" class="btn btn-fundflow-primary py-2 px-3.5 d-inline-flex align-items-center gap-1.5" wire:navigate>
                    <span class="fw-bold">Tambah Baru</span>
                </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="glass-card-subtle p-3 rounded-3 mb-4 border-start border-4 border-success d-flex align-items-center justify-content-between" role="alert">
            <div class="d-flex align-items-center gap-2 text-dark font-medium small">
                <i class="fas fa-check-circle text-success fs-5"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th>Nama Pemohon</th>
                    <th>Instansi</th>
                    <th>Perihal</th>
                    <th>Jadwal</th>
                    <th class="text-center">Status</th>
                    <th class="text-end" style="width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($konsultasis as $index => $konsultasi)
                <tr>
                    <td class="text-center text-muted fw-bold">{{ $konsultasis->firstItem() + $index }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ \Illuminate\Support\Str::limit($konsultasi->nama_pemohon, 28) }}</div>
                        @if($konsultasi->no_telepon)
                            <span class="text-muted" style="font-size: 0.72rem;"><i class="fas fa-phone-alt me-1"></i>{{ $konsultasi->no_telepon }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="text-muted fw-medium">{{ \Illuminate\Support\Str::limit($konsultasi->instansi ?: 'Perorangan', 24) }}</span>
                    </td>
                    <td>
                        <span class="d-inline-block text-truncate fw-medium text-slate-800" style="max-width: 240px;" title="{{ $konsultasi->perihal }}">
                            {{ $konsultasi->perihal }}
                        </span>
                    </td>
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
                                default => 'bg-soft-secondary'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $konsultasi->status }}</span>
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
                                <span class="fw-bold fs-6">⋯</span>
                                <i class="fas fa-caret-down text-muted fs-6"></i>
                            </button>

                            <ul class="dropdown-menu glass-card shadow-lg border-0 p-2" 
                                style="min-width: 150px; z-index: 9999;">
                                
                                <!-- Detail -->
                                <li>
                                    <a href="{{ route('konsultasi.show', $konsultasi) }}" 
                                    class="dropdown-item rounded-2 small py-1.5 px-2 d-flex align-items-center gap-2" 
                                    wire:navigate>
                                        <i class="fas fa-eye text-primary fa-fw"></i>
                                        <span>Detail</span>
                                    </a>
                                </li>

                                <!-- Edit -->
                                @can('update', $konsultasi)
                                <li>
                                    <a href="{{ route('konsultasi.edit', $konsultasi) }}" 
                                    class="dropdown-item rounded-2 small py-1.5 px-2 d-flex align-items-center gap-2" 
                                    wire:navigate>
                                        <i class="fas fa-edit text-info fa-fw"></i>
                                        <span>Edit</span>
                                    </a>
                                </li>
                                @endcan

                                @can('delete', $konsultasi)
                                <li><hr class="dropdown-divider my-1 opacity-25"></li>
                                <!-- Hapus -->
                                <li>
                                    <form action="{{ route('konsultasi.destroy', $konsultasi) }}" 
                                        method="POST" 
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus permohonan konsultasi ini?');" 
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
                    <td colspan="7" class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-comments fa-lg"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tidak ada data konsultasi</h6>
                        <p class="text-muted small mb-3">
                            @if(request('search'))
                                Pencarian dengan kata kunci "{{ request('search') }}" tidak menemukan hasil.
                            @else
                                Belum ada data permohonan konsultasi yang tercatat.
                            @endif
                        </p>
                        @if(request('search'))
                            <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>Reset Pencarian</a>
                        @else
                            @can('create', App\Models\Konsultasi::class)
                                <a href="{{ route('konsultasi.create') }}" class="btn btn-fundflow-primary btn-sm" wire:navigate>+ Tambah Konsultasi Pertama</a>
                            @endcan
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($konsultasis->hasPages())
    <div class="pt-4 mt-2 d-flex justify-content-center">
        {{ $konsultasis->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
