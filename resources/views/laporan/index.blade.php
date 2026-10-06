@extends('layouts.admin')

@section('title', 'Cetak Laporan')
@section('banner_title', 'Cetak Laporan')
@section('banner_subtitle', 'Filter dan cetak dokumen rekapitulasi data konsultasi dan agenda kegiatan.')

@section('content')
<div class="row simonka-fade-in">
    <div class="col-12">
        <!-- Filter Box -->
        <div class="glass-card p-4 p-md-5 mb-4 d-print-none">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white">
                <div>
                    <h4 class="fw-bolder mb-1 text-dark tracking-tight d-flex align-items-center gap-2">
                        Filter Rekapitulasi Data
                    </h4>
                    <p class="text-muted small mb-0">Pilih kriteria rentang tanggal dan jenis data untuk menghasilkan laporan.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="glass-card-subtle p-3 rounded-3 mb-4 border-start border-4 border-danger" role="alert">
                    <ul class="mb-0 text-danger small fw-semibold ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="GET" action="{{ route('laporan.index') }}" class="row g-3 align-items-end">
                <input type="hidden" name="filter" value="1">
                
                <div class="col-md-3">
                    <label class="form-label">Jenis Data</label>
                    <select name="jenis" class="form-select">
                        <option value="konsultasi" {{ $jenis == 'konsultasi' ? 'selected' : '' }}>Permohonan Konsultasi</option>
                        <option value="jadwal" {{ $jenis == 'jadwal' ? 'selected' : '' }}>Jadwal Kegiatan Resmi</option>
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
                    <button type="submit" class="btn btn-fundflow-primary py-2.5">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        @if(request()->has('filter'))
        <!-- Results Card / Printable Area -->
        <div class="glass-card p-4 p-md-5" id="print-area">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom border-white">
                <div>
                    <h5 class="fw-bolder mb-1 text-uppercase text-dark tracking-tight">
                        LAPORAN {{ $jenis === 'jadwal' ? 'JADWAL KEGIATAN RESMI' : 'PERMOHONAN KONSULTASI' }}
                    </h5>
                    <p class="mb-0 text-muted small">
                        Periode: <strong class="text-dark">{{ date('d/m/Y', strtotime($mulai)) }} - {{ date('d/m/Y', strtotime($sampai)) }}</strong> &bull; Status: <strong class="text-dark">{{ $status }}</strong> &bull; Total: <strong class="text-primary">{{ $data->count() }} Data</strong>
                    </p>
                </div>
                <button type="button" class="btn btn-fundflow-primary btn-sm d-print-none py-2 px-3" onclick="window.print()">
                     Cetak / Ekspor PDF
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            @if($jenis == 'jadwal')
                                <th class="text-center" style="width: 170px;">Tanggal & Waktu</th>
                                <th>Nama Kegiatan</th>
                                <th>Lokasi</th>
                            @else
                                <th class="text-center" style="width: 170px;">Tanggal & Waktu</th>
                                <th>Nama Pemohon & Instansi</th>
                                <th>Perihal Pembahasan</th>
                            @endif
                            <th class="text-center" style="width: 120px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="text-center fw-bold text-muted">{{ $index + 1 }}</td>
                            @if($jenis == 'jadwal')
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $item->tanggal->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ date('H:i', strtotime($item->waktu_mulai)) }} WIB</small>
                                </td>
                                <td class="fw-bold text-dark">{{ $item->nama_kegiatan }}</td>
                                <td>
                                    <span class="text-slate-700 fw-medium">
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>{{ $item->lokasi ?: '-' }}
                                    </span>
                                </td>
                            @else
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $item->tanggal_konsultasi->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ date('H:i', strtotime($item->waktu_mulai)) }} WIB</small>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->nama_pemohon }}</div>
                                    <small class="text-muted">{{ $item->instansi ?: 'Perorangan' }}</small>
                                </td>
                                <td>
                                    <span class="text-slate-800 fw-medium">{{ $item->perihal }}</span>
                                </td>
                            @endif
                            <td class="text-center">
                                @php
                                    $badge = match($item->status) {
                                        'Menunggu' => 'bg-soft-warning',
                                        'Terjadwal', 'Disetujui' => 'bg-soft-info',
                                        'Berlangsung' => 'bg-soft-primary',
                                        'Selesai' => 'bg-soft-success',
                                        'Ditolak' => 'bg-soft-danger',
                                        'Dibatalkan' => 'bg-soft-secondary',
                                        default => 'bg-soft-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $item->status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="empty-state">
                                <div class="empty-state-icon">
                                    <i class="fas fa-file-invoice fa-lg"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Tidak ada data ditemukan</h6>
                                <p class="text-muted small mb-0">Tidak ada record yang sesuai dengan kriteria filter yang Anda tentukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
            margin: 0 !important;
            padding: 1.5rem !important;
            background: #ffffff !important;
            box-shadow: none !important;
            border: none !important;
        }
        .table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .table th, .table td {
            border: 1px solid #999 !important;
            color: #000 !important;
            padding: 6px 10px !important;
        }
    }
</style>
@endsection
