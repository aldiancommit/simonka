@extends('layouts.admin')

@section('title', 'Ajukan Reschedule')
@section('banner_title', 'Penjadwalan Ulang')
@section('banner_subtitle', 'Formulir pengajuan perubahan jadwal dan waktu konsultasi.')

@section('banner_action')
    <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-fundflow-glass" wire:navigate>
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white">
                <div>
                    <h4 class="fw-bolder mb-1 text-dark tracking-tight">Form Pengajuan Penjadwalan Ulang</h4>
                    <p class="text-muted small mb-0">Pilih permohonan konsultasi yang ingin diubah jadwalnya dan tentukan waktu baru.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('penjadwalan-ulang.store') }}">
                @csrf
                <div class="row mb-4">
                    <div class="col-md-8 mb-3">
                        <label class="form-label">Pilih Permohonan Konsultasi <span class="text-danger">*</span></label>
                        <select name="konsultasi_id" id="konsultasi_select" class="form-select @error('konsultasi_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Konsultasi --</option>
                            @foreach($konsultasis as $k)
                                <option value="{{ $k->id }}" 
                                        data-tanggal="{{ $k->tanggal_konsultasi->format('Y-m-d') }}"
                                        {{ old('konsultasi_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_pemohon }} - {{ \Illuminate\Support\Str::limit($k->perihal, 40) }} ({{ $k->tanggal_konsultasi->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('konsultasi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Sebelumnya</label>
                        <input type="date" id="tanggal_lama_input" class="form-control" readonly disabled style="background: rgba(255,255,255,0.4) !important;">
                        <small class="text-muted">Terisi otomatis sesuai data konsultasi.</small>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Baru <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_baru" class="form-control @error('tanggal_baru') is-invalid @enderror" value="{{ old('tanggal_baru', date('Y-m-d')) }}" required>
                        @error('tanggal_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Waktu Mulai Baru <span class="text-danger">*</span></label>
                        <input type="time" name="waktu_mulai_baru" class="form-control @error('waktu_mulai_baru') is-invalid @enderror" value="{{ old('waktu_mulai_baru', '10:00') }}" required>
                        @error('waktu_mulai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Waktu Selesai Baru</label>
                        <input type="time" name="waktu_selesai_baru" class="form-control @error('waktu_selesai_baru') is-invalid @enderror" value="{{ old('waktu_selesai_baru') }}">
                        @error('waktu_selesai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Alasan Penjadwalan Ulang <span class="text-danger">*</span></label>
                        <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" placeholder="Jelaskan alasan pengajuan reschedule (misal: bentrok kegiatan pimpinan, permohonan pemohon, penyesuaian materi rapat)" required>{{ old('alasan') }}</textarea>
                        @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-3 border-top border-white">
                    <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-fundflow-glass" wire:navigate>Batal</a>
                    <button type="submit" class="btn btn-fundflow-primary px-4">
                        <i class="fas fa-paper-plane me-1"></i> Ajukan Reschedule
                    </button>
                </div>
            </form>
</div>

<script>
    const bindDateSync = () => {
        const select = document.getElementById('konsultasi_select');
        const input = document.getElementById('tanggal_lama_input');

        const syncOldDate = () => {
            if (!select || !input) return;
            const selected = select.options[select.selectedIndex];
            if (selected && selected.dataset.tanggal) {
                input.value = selected.dataset.tanggal;
            }
        };

        if (select) {
            select.addEventListener('change', syncOldDate);
            syncOldDate();
        }
    };

    document.addEventListener('DOMContentLoaded', bindDateSync);
    document.addEventListener('livewire:navigated', bindDateSync);
</script>
@endsection
