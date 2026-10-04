@extends('layouts.admin')
@section('title', 'Data Konsultasi')
@section('banner_title', 'Konsultasi')
@section('banner_subtitle', 'Manajemen daftar konsultasi')
@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
        <div class="header-title">
            <h4 class="card-title mb-0">Daftar Konsultasi</h4>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" class="d-flex">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cari</button>
            </form>
            <a href="{{ route('konsultasi.create') }}" class="btn btn-sm btn-primary text-nowrap" wire:navigate>+ Tambah</a>
        </div>
    </div>
    <div class="card-body p-0 mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap" style="width: 50px;">No</th>
                        <th class="text-nowrap">Nama Pemohon</th>
                        <th class="text-nowrap">Instansi</th>
                        <th>Perihal</th>
                        <th class="text-nowrap">Tanggal & Waktu</th>
                        <th class="text-nowrap text-center">Status</th>
                        <th class="text-nowrap text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($konsultasis as $index => $konsultasi)
                    <tr>
                        <td class="text-nowrap">{{ $konsultasis->firstItem() + $index }}</td>
                        <td class="text-nowrap fw-semibold">{{ \Illuminate\Support\Str::limit($konsultasi->nama_pemohon, 25) }}</td>
                        <td class="text-nowrap text-muted">{{ \Illuminate\Support\Str::limit($konsultasi->instansi ?: '-', 20) }}</td>
                        <td>
                            <span class="d-inline-block text-truncate" style="max-width: 200px;" title="{{ $konsultasi->perihal }}">
                                {{ $konsultasi->perihal }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <div class="d-flex flex-column">
                                <span>{{ $konsultasi->tanggal_konsultasi->format('d/m/Y') }}</span>
                                <small class="text-muted">{{ date('H:i', strtotime($konsultasi->waktu_mulai)) }} - {{ $konsultasi->waktu_selesai ? date('H:i', strtotime($konsultasi->waktu_selesai)) : 'Selesai' }}</small>
                            </div>
                        </td>
                        <td class="text-nowrap text-center">
                            @php
                                $badge = match($konsultasi->status) {
                                    'Menunggu' => 'warning',
                                    'Disetujui' => 'info',
                                    'Selesai' => 'success',
                                    'Ditolak' => 'danger',
                                    'Dibatalkan' => 'secondary',
                                    default => 'light'
                                };
                            @endphp
                            <span class="badge bg-{{ $badge }}">{{ $konsultasi->status }}</span>
                        </td>
                        <td class="text-nowrap text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('konsultasi.edit', $konsultasi) }}" class="btn btn-sm btn-info" wire:navigate>Edit</a>
                                <form action="{{ route('konsultasi.destroy', $konsultasi) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada data konsultasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white border-top-0 pt-3 pb-3">
        {{ $konsultasis->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
