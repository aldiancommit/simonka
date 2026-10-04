@extends('layouts.admin')

@section('title', 'Riwayat & Arsip')
@section('banner_title', 'Riwayat & Arsip Kegiatan')
@section('banner_subtitle', 'Arsip dan histori kegiatan resmi serta konsultasi yang telah selesai.')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <ul class="nav nav-pills" id="riwayat-tabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $tab == 'konsultasi' ? 'active bg-primary' : 'bg-light text-dark' }}" href="{{ route('riwayat.index', ['tab' => 'konsultasi']) }}" wire:navigate>
                                Arsip Konsultasi
                            </a>
                        </li>
                        <li class="nav-item ms-2">
                            <a class="nav-link {{ $tab == 'jadwal' ? 'active bg-primary' : 'bg-light text-dark' }}" href="{{ route('riwayat.index', ['tab' => 'jadwal']) }}" wire:navigate>
                                Arsip Jadwal Kegiatan
                            </a>
                        </li>
                    </ul>
                    <form method="GET" class="d-flex">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari arsip..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-sm btn-outline-primary ms-2">Cari</button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap" style="width: 50px;">No</th>
                                @if($tab == 'jadwal')
                                    <th class="text-nowrap">Nama Kegiatan</th>
                                    <th class="text-nowrap text-center">Tanggal</th>
                                    <th>Lokasi</th>
                                @else
                                    <th class="text-nowrap">Pemohon & Instansi</th>
                                    <th>Perihal</th>
                                    <th class="text-nowrap text-center">Tanggal</th>
                                @endif
                                <th class="text-nowrap text-center">Status Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                            <tr>
                                <td class="text-nowrap">{{ $data->firstItem() + $index }}</td>
                                @if($tab == 'jadwal')
                                    <td class="fw-semibold">
                                        <span class="d-inline-block text-truncate" style="max-width: 250px;" title="{{ $item->nama_kegiatan }}">{{ $item->nama_kegiatan }}</span>
                                    </td>
                                    <td class="text-nowrap text-center">{{ $item->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="d-inline-block text-truncate text-muted" style="max-width: 200px;" title="{{ $item->lokasi }}">{{ $item->lokasi ?: '-' }}</span>
                                    </td>
                                @else
                                    <td>
                                        <div class="fw-semibold text-nowrap">{{ \Illuminate\Support\Str::limit($item->nama_pemohon, 25) }}</div>
                                        <div class="text-muted text-nowrap" style="font-size: 0.85rem;">{{ \Illuminate\Support\Str::limit($item->instansi ?: '-', 25) }}</div>
                                    </td>
                                    <td>
                                        <span class="d-inline-block text-truncate" style="max-width: 250px;" title="{{ $item->perihal }}">{{ $item->perihal }}</span>
                                    </td>
                                    <td class="text-nowrap text-center">{{ $item->tanggal_konsultasi->format('d/m/Y') }}</td>
                                @endif
                                <td class="text-nowrap text-center">
                                    @php
                                        $badge = match($item->status) {
                                            'Selesai' => 'success',
                                            'Ditolak' => 'danger',
                                            'Dibatalkan' => 'secondary',
                                            default => 'light'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badge }}">{{ $item->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada riwayat/arsip ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-3 pb-3">
                {{ $data->appends(['tab' => $tab, 'search' => request('search')])->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
