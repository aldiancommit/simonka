@extends('layouts.admin')
@section('title', 'Ajukan Reschedule')
@section('banner_title', 'Penjadwalan Ulang')
@section('banner_subtitle', 'Ajukan reschedule konsultasi')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="header-title">
            <h4 class="card-title">Form Pengajuan Reschedule</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('penjadwalan-ulang.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Konsultasi <span class="text-danger">*</span></label>
                    <select name="konsultasi_id" class="form-select @error('konsultasi_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Konsultasi --</option>
                        @foreach($konsultasis as $k)
                            <option value="{{ $k->id }}" {{ old('konsultasi_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_pemohon }} - {{ $k->perihal }} ({{ $k->tanggal_konsultasi->format('d/m/Y') }})
                            </option>
                        @endforeach
                    </select>
                    @error('konsultasi_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Lama <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_lama" class="form-control @error('tanggal_lama') is-invalid @enderror" value="{{ old('tanggal_lama') }}" required>
                    @error('tanggal_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Baru <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_baru" class="form-control @error('tanggal_baru') is-invalid @enderror" value="{{ old('tanggal_baru') }}" required>
                    @error('tanggal_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Mulai Baru <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai_baru" class="form-control @error('waktu_mulai_baru') is-invalid @enderror" value="{{ old('waktu_mulai_baru') }}" required>
                    @error('waktu_mulai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Waktu Selesai Baru</label>
                    <input type="time" name="waktu_selesai_baru" class="form-control @error('waktu_selesai_baru') is-invalid @enderror" value="{{ old('waktu_selesai_baru') }}">
                    @error('waktu_selesai_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Alasan Reschedule <span class="text-danger">*</span></label>
                    <textarea name="alasan" class="form-control @error('alasan') is-invalid @enderror" rows="3" required>{{ old('alasan') }}</textarea>
                    @error('alasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-light" wire:navigate>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
