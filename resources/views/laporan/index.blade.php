@extends('layouts.admin')

@section('title', 'Cetak Laporan Rekapitulasi')
@section('banner_title', 'Cetak Laporan Rekapitulasi')
@section('banner_subtitle', 'Filter dan cetak dokumen rekapitulasi data konsultasi dan agenda kegiatan resmi.')

@section('content')
<div class="row simonka-fade-in">
    <div class="col-12">
        <!-- Filter Box (Hanya Tampil di Layar / d-print-none) -->
        <div class="glass-card p-4 p-md-5 mb-4 d-print-none">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white">
                <div>
                    <h4 class="fw-bolder mb-1 text-dark tracking-tight d-flex align-items-center gap-2">
                        <i class="fas fa-filter text-primary-glass"></i> Filter Rekapitulasi Data
                    </h4>
                    <p class="text-muted small mb-0">Pilih kriteria rentang tanggal, jenis data, dan status untuk menghasilkan dokumen laporan resmi.</p>
                </div>
            </div>

            @if ($errors->any())
                <div class="simonka-alert simonka-alert-danger mb-4" role="alert">
                    <div class="simonka-alert-icon">
                        <i class="fas fa-exclamation"></i>
                    </div>
                    <div class="simonka-alert-content">
                        <div class="simonka-alert-title">Parameter Filter Tidak Valid</div>
                        <div class="simonka-alert-message">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="simonka-alert-close" onclick="this.closest('.simonka-alert').remove()" aria-label="Tutup">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <form method="GET" action="{{ route('laporan.index') }}" class="row g-3 align-items-end">
                <input type="hidden" name="filter" value="1">
                
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small">Jenis Data Laporan</label>
                    <select name="jenis" class="form-select glass-pill text-dark" onchange="this.form.submit()">
                        <option value="konsultasi" {{ $jenis == 'konsultasi' ? 'selected' : '' }}>Permohonan Konsultasi</option>
                        <option value="jadwal" {{ $jenis == 'jadwal' ? 'selected' : '' }}>Jadwal Kegiatan Resmi</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small">Tanggal Mulai</label>
                    <input type="date" name="mulai" class="form-control glass-pill text-dark" value="{{ $mulai }}" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold text-dark small">Tanggal Sampai</label>
                    <input type="date" name="sampai" class="form-control glass-pill text-dark" value="{{ $sampai }}" required>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label fw-bold text-dark small">Status</label>
                    <select name="status" class="form-select glass-pill text-dark">
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
                    <button type="submit" class="btn btn-fundflow-primary py-2.5 shadow-sm">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        @if(request()->has('filter'))
        <!-- Document Print Container -->
        <div class="print-sheet p-4 p-md-5 mb-5 shadow-sm" id="print-area">
            
            <!-- Toolbar Aksi Cetak (Hanya Tampil di Layar / d-print-none) -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-3 border-bottom d-print-none">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-soft-primary text-primary px-3 py-2 fw-semibold">
                        Pratinjau Dokumen Cetak
                    </span>
                    <span class="text-muted small">
                        Total: <strong>{{ $data->count() }} Data</strong> tercatat
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-fundflow-primary btn-sm py-2 px-3.5 d-inline-flex align-items-center gap-2 shadow-xs" onclick="window.print()">
                        <i class="fas fa-print"></i>
                        <span class="fw-bold">Cetak / Simpan PDF</span>
                    </button>
                </div>
            </div>

            <!-- ================= KOP SURAT RESMI KEDINASAN ================= -->
            <div class="kop-surat mb-3">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div class="kop-logo flex-shrink-0 text-center" style="width: 80px;">
                        <img src="{{ asset('assets/images/logo-palu.png') }}" 
                             alt="Logo Pemerintah Kota Palu" 
                             style="width: 72px; height: auto; object-fit: contain;">
                    </div>
                    <div class="kop-text text-center flex-grow-1 px-2">
                        <h6 class="kop-instansi-prov mb-0 fw-bold text-uppercase" style="font-size: 1rem; letter-spacing: 0.05em; color: #111827; font-family: 'Arial', sans-serif;">
                            PEMERINTAH KOTA PALU
                        </h6>
                        <h4 class="kop-instansi-main mb-0 fw-bolder text-uppercase" style="font-size: 1.25rem; letter-spacing: 0.02em; color: #000000; font-family: 'Arial', sans-serif;">
                            BADAN KESATUAN BANGSA DAN POLITIK (KESBANGPOL)
                        </h4>
                        <div class="kop-app-name fw-bold text-uppercase mt-0.5" style="font-size: 0.82rem; letter-spacing: 0.05em; color: #1e40af; font-family: 'Arial', sans-serif;">
                            SISTEM INFORMASI MONITORING KONSULTASI & AGENDA PIMPINAN (SIMONKA)
                        </div>
                        <p class="kop-alamat mb-0 text-muted small mt-1" style="font-size: 0.74rem; line-height: 1.35; color: #374151 !important; font-family: 'Arial', sans-serif;">
                            Jl. Balai Kota No. 1, Tanamodindi, Kec. Mantikulore, Kota Palu, Sulawesi Tengah 94118<br>
                            Laman Resmi: <em>kesbangpol.palukota.go.id</em> | Pos-el: <em>kesbangpol@palukota.go.id</em>
                        </p>
                    </div>
                    <div class="kop-logo-spacer flex-shrink-0 d-none d-sm-block" style="width: 80px;"></div>
                </div>
                {{-- Garis Ganda Pembatas Kop Surat Resmi --}}
                <div class="kop-divider"></div>
            </div>

            <!-- ================= JUDUL & METADATA LAPORAN ================= -->
            <div class="text-center mb-4">
                <h5 class="fw-bolder text-uppercase mb-1" style="font-size: 1.125rem; letter-spacing: 0.03em; color: #000000;">
                    LAPORAN REKAPITULASI {{ $jenis === 'jadwal' ? 'AGENDA KEGIATAN RESMI' : 'PERMOHONAN KONSULTASI' }}
                </h5>
                <div class="fw-semibold text-dark" style="font-size: 0.92rem;">
                    Periode: {{ \Carbon\Carbon::parse($mulai)->locale('id')->translatedFormat('d F Y') }} s.d. {{ \Carbon\Carbon::parse($sampai)->locale('id')->translatedFormat('d F Y') }}
                </div>
            </div>

            <!-- ================= TABEL DATA LAPORAN ================= -->
            <div class="table-container mb-4">
                <table class="table-laporan">
                    <thead>
                        <tr>
                            <th class="text-center col-no" style="width: 5%;">No</th>
                            @if($jenis == 'jadwal')
                                <th class="text-center col-waktu" style="width: 19%;">Tanggal & Waktu</th>
                                <th class="col-nama" style="width: 28%;">Nama Kegiatan</th>
                                <th class="col-lokasi" style="width: 19%;">Lokasi</th>
                                <th class="text-center col-status" style="width: 13%;">Status</th>
                                <th class="col-ket" style="width: 18%;">Keterangan</th>
                            @else
                                <th class="text-center col-waktu" style="width: 17%;">Tanggal & Waktu</th>
                                <th class="col-pemohon" style="width: 23%;">Pemohon & Instansi</th>
                                <th class="col-perihal" style="width: 26%;">Perihal</th>
                                <th class="text-center col-status" style="width: 13%;">Status</th>
                                <th class="col-catatan" style="width: 16%;">Catatan Disposisi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $index => $item)
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            @if($jenis == 'jadwal')
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $item->tanggal->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">
                                        {{ date('H:i', strtotime($item->waktu_mulai)) }}@if($item->waktu_selesai) - {{ date('H:i', strtotime($item->waktu_selesai)) }}@endif WIB
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark text-break">{{ $item->nama_kegiatan }}</div>
                                </td>
                                <td>
                                    <span class="text-break">{{ $item->lokasi ?: '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark">{{ $item->status }}</span>
                                </td>
                                <td>
                                    <span class="text-muted text-break" style="font-size: 0.8rem;">
                                        {{ $item->keterangan ?: '-' }}
                                    </span>
                                </td>
                            @else
                                <td class="text-center">
                                    <div class="fw-bold text-dark">{{ $item->tanggal_konsultasi->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">
                                        {{ date('H:i', strtotime($item->waktu_mulai)) }}@if($item->waktu_selesai) - {{ date('H:i', strtotime($item->waktu_selesai)) }}@endif WIB
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark text-break">{{ $item->nama_pemohon }}</div>
                                    <div class="text-muted text-break" style="font-size: 0.78rem;">{{ $item->instansi ?: 'Perorangan' }}</div>
                                    @if($item->no_telepon)
                                        <div class="text-muted text-break" style="font-size: 0.74rem;">Telp: {{ $item->no_telepon }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-dark text-break" style="line-height: 1.4;">{{ $item->perihal }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark">{{ $item->status }}</span>
                                </td>
                                <td>
                                    <div class="text-muted text-break" style="font-size: 0.8rem; line-height: 1.35;">
                                        {{ $item->catatan ?: '-' }}
                                    </div>
                                </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <div class="py-3">
                                    <i class="fas fa-folder-open fa-2x mb-2 text-muted opacity-50"></i>
                                    <h6 class="fw-bold text-dark mb-1">Tidak Ada Data Ditemukan</h6>
                                    <p class="small text-muted mb-0">Tidak ada data rekapitulasi yang sesuai dengan rentang tanggal dan status yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ================= STATISTIK RINGKASAN ================= -->
            @if($data->isNotEmpty())
            <div class="rekap-summary-box p-3 rounded-0 mb-4 page-break-avoid">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <span class="fw-bold text-uppercase text-dark" style="font-size: 0.78rem; letter-spacing: 0.04em;">
                        Rekapitulasi Status (Total: {{ $data->count() }} Data):
                    </span>
                    <div class="d-flex flex-wrap gap-3" style="font-size: 0.82rem;">
                        @foreach($data->groupBy('status') as $statusName => $items)
                            <span class="text-dark">
                                {{ $statusName }}: <strong>{{ $items->count() }}</strong>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- ================= PENGESAHAN & TANDA TANGAN ================= -->
            <div class="row mt-4 pt-3 page-break-avoid" style="page-break-inside: avoid;">
                <div class="col-7">
                    <div class="text-muted" style="font-size: 0.78rem; line-height: 1.5;">
                        <strong class="text-dark">Keterangan Dokumen:</strong><br>
                        &bull; Laporan ini dicetak secara resmi melalui Aplikasi SIMONKA.<br>
                        &bull; Tanggal & Waktu Cetak: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB<br>
                        &bull; Operator / Petugas Cetak: {{ auth()->user()?->name ?? 'Administrator' }} ({{ auth()->user()?->role?->label() ?? 'Admin' }})
                    </div>
                </div>
                <div class="col-5 text-center">
                    <div style="font-size: 0.88rem; color: #111827; line-height: 1.4;">
                        Palu, {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y') }}<br>
                        <span class="fw-bold">Kepala Badan Kesatuan Bangsa dan Politik<br>Kota Palu</span>
                        
                        <div style="height: 65px;"></div> {{-- Ruang Tanda Tangan --}}
                        
                        <span class="fw-bolder text-decoration-underline d-block" style="font-size: 0.94rem; color: #000000;">
                            ( ............................................ )
                        </span>
                        <span class="text-muted d-block mt-0.5" style="font-size: 0.8rem;">
                            NIP. .....................................................
                        </span>
                    </div>
                </div>
            </div>

        </div>
        @endif
    </div>
</div>

<style>
    /* Container on Web Screen */
    .print-sheet {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.95);
        border-radius: 12px;
        color: #1e293b;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .kop-divider {
        border-top: 3px solid #111827;
        border-bottom: 1px solid #111827;
        height: 6px;
        margin-top: 10px;
        margin-bottom: 16px;
    }

    .table-container {
        width: 100%;
        overflow-x: hidden;
    }

    .table-laporan {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
        font-size: 0.85rem;
        box-sizing: border-box;
    }

    .table-laporan th {
        background-color: #f8fafc;
        color: #0f172a;
        font-weight: 700;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border: 1px solid #cbd5e1;
        padding: 10px 8px;
        vertical-align: middle;
        box-sizing: border-box;
        word-break: normal;
        overflow-wrap: normal;
        white-space: normal;
    }

    .table-laporan th.col-no,
    .table-laporan th.col-waktu,
    .table-laporan th.col-status {
        white-space: nowrap;
    }

    .table-laporan td {
        border: 1px solid #cbd5e1;
        padding: 8px 8px;
        vertical-align: middle;
        color: #1e293b;
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: normal;
        white-space: normal;
        box-sizing: border-box;
    }

    .table-laporan tbody tr:nth-child(even) {
        background-color: #fcfdfe;
    }

    .rekap-summary-box {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        box-sizing: border-box;
    }

    /* Print Specific Media Query */
    @media print {
        @page {
            size: A4 portrait;
            margin: 12mm 10mm 12mm 10mm;
        }

        html, body {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            color: #000000 !important;
        }

        body * {
            visibility: hidden !important;
        }

        #print-area,
        #print-area * {
            visibility: visible !important;
        }

        #print-area {
            position: static !important;
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #ffffff !important;
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            box-sizing: border-box !important;
        }

        .d-print-none {
            display: none !important;
        }

        .kop-divider {
            border-top: 3px solid #000000 !important;
            border-bottom: 1px solid #000000 !important;
            height: 5px !important;
            margin-top: 8px !important;
            margin-bottom: 12px !important;
        }

        .table-container {
            width: 100% !important;
            overflow: visible !important;
            display: block !important;
            box-sizing: border-box !important;
        }

        .table-laporan {
            width: 100% !important;
            table-layout: fixed !important;
            border: 1px solid #000000 !important;
            font-size: 9.5pt !important;
            border-collapse: collapse !important;
            box-sizing: border-box !important;
        }

        .table-laporan th {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color: #000000 !important;
            border: 1px solid #000000 !important;
            padding: 7px 7px !important;
            font-weight: 700 !important;
            box-sizing: border-box !important;
            word-break: normal !important;
            overflow-wrap: normal !important;
        }

        .table-laporan th.col-no,
        .table-laporan th.col-waktu,
        .table-laporan th.col-status {
            white-space: nowrap !important;
        }

        .table-laporan td {
            border: 1px solid #000000 !important;
            color: #000000 !important;
            padding: 6px 7px !important;
            line-height: 1.35 !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            word-break: normal !important;
            white-space: normal !important;
            box-sizing: border-box !important;
        }

        .rekap-summary-box {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            border: 1px solid #000000 !important;
            padding: 6px 8px !important;
            box-sizing: border-box !important;
        }

        .page-break-avoid {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
    }
</style>
@endsection
