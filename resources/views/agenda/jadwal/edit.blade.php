@extends('layouts.admin')
@section('title', 'Edit Jadwal Kegiatan')
@section('banner_title', 'Jadwal Kegiatan')
@section('banner_subtitle', 'Edit data jadwal')
@section('content')
<div class="card">
    <div class="card-header">
        <div class="header-title">
            <h4 class="card-title">Form Edit Jadwal</h4>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('agenda.jadwal.update', $jadwalKegiatan) }}">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" name="nama_kegiatan" class="form-control @error('nama_kegiatan') is-invalid @enderror" value="{{ old('nama_kegiatan', $jadwalKegiatan->nama_kegiatan) }}" required>
                    @error('nama_kegiatan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror" value="{{ old('tanggal', $jadwalKegiatan->tanggal->format('Y-m-d')) }}" required>
                    @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai', date('H:i', strtotime($jadwalKegiatan->waktu_mulai))) }}" required>
                    @error('waktu_mulai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai', $jadwalKegiatan->waktu_selesai ? date('H:i', strtotime($jadwalKegiatan->waktu_selesai)) : '') }}">
                    @error('waktu_selesai')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $jadwalKegiatan->lokasi) }}">
                    @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        @foreach(['Terjadwal', 'Berlangsung', 'Selesai', 'Dibatalkan'] as $status)
                            <option value="{{ $status }}" {{ old('status', $jadwalKegiatan->status) === $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $jadwalKegiatan->keterangan) }}</textarea>
                    @error('keterangan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-light" wire:navigate>Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
