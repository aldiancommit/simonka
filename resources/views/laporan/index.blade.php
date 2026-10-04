@extends('layouts.admin')

@section('title', 'Cetak Laporan')
@section('banner_title', 'Cetak Laporan')
@section('banner_subtitle', 'Filter dan cetak laporan agenda serta konsultasi.')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4 d-print-none">
            <div class="card-header bg-white border-bottom pt-4 pb-3">
                <h4 class="card-title mb-0">Filter Laporan</h4>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('laporan.index') }}" class="row g-3 align-items-end">
                    <input type="hidden" name="filter" value="1">
                    
                    <div class="col-md-3">
                        <label class="form-label">Jenis Data</label>
                        <select name="jenis" class="form-select">
                            <option value="konsultasi" {{ $jenis == 'konsultasi' ? 'selected' : '' }}>Konsultasi</option>
                            <option value="jadwal" {{ $jenis == 'jadwal' ? 'selected' : '' }}>Jadwal Kegiatan</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="mulai" class="form-control" value="{{ $mulai }}" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Sampai</label>
                        <input type="date" name="sampai" class="form-control" value="{{ $sampai }}" required>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="Semua" {{ $status == 'Semua' ? 'selected' : '' }}>Semua Status</option>
                            @if($jenis == 'konsultasi')
                                <option value="Menunggu" {{ $status == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="Disetujui" {{ $status == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                                <option value="Dibatalkan" {{ $status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            @else
                                <option value="Terjadwal" {{ $status == 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                <option value="Berlangsung" {{ $status == 'Berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                                <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="Dibatalkan" {{ $status == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                            @endif
                        </select>
                    </div>
                    
                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        @if(request()->has('filter'))
        <div class="card shadow-sm border-0" id="print-area">
            <div class="card-header bg-white border-bottom pt-4 pb-3 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1 text-uppercase">LAPORAN {{ strtoupper($jenis) }}</h4>
                    <p class="mb-0 text-muted small">Periode: {{ date('d/m/Y', strtotime($mulai)) }} - {{ date('d/m/Y', strtotime($sampai)) }} | Status: {{ $status }}</p>
                </div>
                <button type="button" class="btn btn-success d-print-none" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Cetak Dokumen
                </button>
            </div>
            <div class="card-body p-0 mt-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap text-center" style="width: 50px;">No</th>
                                @if($jenis == 'jadwal')
                                    <th class="text-nowrap">Tanggal & Waktu</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Lokasi</th>
                                @else
                                    <th class="text-nowrap text-center">Tanggal & Waktu</th>
                                    <th>Nama Pemohon (Instansi)</th>
                                    <th>Perihal</th>
                                @endif
                                <th class="text-nowrap text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $index => $item)
                            <tr>
                                <td class="text-nowrap text-center">{{ $index + 1 }}</td>
                                @if($jenis == 'jadwal')
                                    <td class="text-nowrap">{{ $item->tanggal->format('d/m/Y') }} <small class="text-muted d-block">{{ date('H:i', strtotime($item->waktu_mulai)) }}</small></td>
                                    <td>{{ $item->nama_kegiatan }}</td>
                                    <td>{{ $item->lokasi ?: '-' }}</td>
                                @else
                                    <td class="text-nowrap text-center">{{ $item->tanggal_konsultasi->format('d/m/Y') }} <small class="text-muted d-block">{{ date('H:i', strtotime($item->waktu_mulai)) }}</small></td>
                                    <td>
                                        <div class="fw-semibold">{{ $item->nama_pemohon }}</div>
                                        <div class="text-muted" style="font-size: 0.85rem;">{{ $item->instansi ?: '-' }}</div>
                                    </td>
                                    <td>{{ $item->perihal }}</td>
                                @endif
                                <td class="text-nowrap text-center">
                                    <span class="badge bg-light text-dark border border-secondary">{{ $item->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Tidak ada data untuk filter tersebut.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .card-header, .card {
            border: none !important;
            box-shadow: none !important;
        }
        .table-responsive {
            overflow-x: visible !important;
        }
        .table {
            border-color: #000 !important;
        }
        .table th, .table td {
            border: 1px solid #000 !important;
            color: #000 !important;
        }
    }
</style>

<script>
    // Simple script to refresh page on jenis change to update status dropdown options
    document.querySelector('select[name="jenis"]').addEventListener('change', function() {
        // Clear filter flag to just reload form
        const form = this.closest('form');
        form.querySelector('input[name="filter"]').value = "0";
        form.submit();
    });
</script>
@endsection
