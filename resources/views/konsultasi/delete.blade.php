@extends('layouts.admin')

@section('title', 'Hapus Konsultasi')
@section('banner_title', 'Hapus Data Konsultasi')
@section('banner_subtitle', 'Konfirmasi penghapusan data permohonan konsultasi.')

@section('banner_action')
    <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white">
        <div>
            <h4 class="fw-bolder mb-1 text-danger tracking-tight">
                <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus Data Konsultasi #{{ $id ?? '' }}
            </h4>
            <p class="text-muted small mb-0">Tindakan ini akan menghapus data permohonan konsultasi dari sistem.</p>
        </div>
    </div>

    <div class="glass-card-subtle p-4 rounded-4 text-center my-4">
        <div class="empty-state-icon text-danger mb-3">
            <i class="fas fa-trash-alt fa-2x"></i>
        </div>
        <h5 class="fw-bold text-dark mb-2">Apakah Anda yakin ingin menghapus data ini?</h5>
        <p class="text-muted small mb-0">Data yang sudah dihapus tidak dapat dipulihkan kembali.</p>
    </div>

    <div class="d-flex justify-content-end gap-2 pt-3 border-top border-white">
        <a href="{{ route('konsultasi.index') }}" class="btn btn-fundflow-glass" wire:navigate>Batal</a>
        <button type="button" class="btn btn-danger px-4">
            <i class="fas fa-trash-alt me-1"></i> Hapus Sekarang
        </button>
    </div>
</div>
@endsection
