@extends('layouts.admin')

@section('title', 'Review Reschedule')
@section('banner_title', 'Review Reschedule')
@section('banner_subtitle', 'Persetujuan atau pembaruan pengajuan perubahan jadwal konsultasi.')

@section('banner_action')
    <div class="d-flex gap-2">
        <a href="{{ route('penjadwalan-ulang.show', $penjadwalanUlang) }}" class="btn btn-fundflow-glass" wire:navigate>
            <i class="fas fa-eye me-1"></i> Detail
        </a>
        <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-fundflow-glass" wire:navigate>
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
@endsection

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 pb-2 border-bottom border-white gap-2">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Review Penjadwalan Ulang</h4>
            <p class="text-muted small mb-0">Tentukan status persetujuan atau perbarui usulan waktu di bawah ini.</p>
        </div>
        @php
            $badge = match($penjadwalanUlang->status) {
                'Menunggu' => 'bg-soft-warning',
                'Disetujui' => 'bg-soft-info',
                'Ditolak' => 'bg-soft-danger',
                default => 'bg-soft-secondary'
            };
        @endphp
        <span class="badge {{ $badge }} px-3 py-1.5">Status: {{ $penjadwalanUlang->status }}</span>
    </div>

    <form method="POST" action="{{ route('penjadwalan-ulang.update', $penjadwalanUlang) }}">
        @csrf
        @method('PUT')
        
        <div class="row mb-4">
            <div class="col-md-8 mb-3">
                <label class="form-label">Permohonan Konsultasi <span class="text-danger">*</span></label>
                <select name="konsultasi_id" id="konsultasi_select" class="form-select @error('konsultasi_id') is-invalid @enderror" required>
                    @foreach($konsultasis as $k)
                        <option value="{{ $k->id }}" 
                                data-tanggal="{{ $k->tanggal_konsultasi->format('Y-m-d') }}"
                                {{ old('konsultasi_id', $penjadwalanUlang->konsultasi_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_pemohon }} - {{ \Illuminate\Support\Str::limit($k->perihal, 40) }} ({{ $k->tanggal_konsultasi->format('d/m/Y') }})
                        </option>
                    @endforeach
                </select>
                @error('konsultasi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Sebelumnya <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_lama" id="tanggal_lama_input" class="form-control @error('tanggal_lama') is-invalid @enderror" value="{{ old('tanggal_lama', $penjadwalanUlang->tanggal_lama->format('Y-m-d')) }}" required style="background: rgba(255,255,255,0.4) !important;">
                @error('tanggal_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Baru <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_baru" class="form-control @error('tanggal_baru') is-invalid @enderror" value="{{ old('tanggal_baru', $penjadwalanUlang->tanggal_baru->format('Y-m-d')) }}" required>
                @error('tanggal_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Waktu Mulai Baru <span class="text-danger">*</span></label>
                <input type="time" name="waktu_mulai_baru" class="form-control @error('waktu_mulai_baru') is-invalid @enderror" value="{{ old('waktu_mulai_baru', date('H:i', strtotime($penjadwalanUlang->waktu_mulai_baru))) }}" required>
                @error('waktu_mulai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Waktu Selesai Baru</label>
                <input type="time" name="waktu_selesai_baru" class="form-control @error('waktu_selesai_baru') is-invalid @enderror" value="{{ old('waktu_selesai_baru', $penjadwalanUlang->waktu_selesai_baru ? date('H:i', strtotime($penjadwalanUlang->waktu_selesai_baru)) : '') }}">
                @error('waktu_selesai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Status Keputusan <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    @foreach(['Menunggu', 'Disetujui', 'Ditolak'] as $st)
                        <option value="{{ $st }}" {{ old('status', $penjadwalanUlang->status) === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
                <small class="text-muted d-block mt-1">Status <strong>Disetujui</strong> otomatis menyinkronkan jadwal konsultasi.</small>
                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label">Alasan Penjadwalan Ulang <span class="text-danger">*</span></label>
                <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="2" required>{{ old('alasan', $penjadwalanUlang->alasan) }}</textarea>
                @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top border-white">
            <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-fundflow-glass" wire:navigate>Batal</a>
            <button type="submit" class="btn btn-fundflow-primary px-4">
                <i class="fas fa-check-circle me-1"></i> Simpan & Update Keputusan
            </button>
        </div>
    </form>
</div>
@endsection
