@extends('layouts.admin')

@section('title', 'Tambah Arsip')
@section('banner_title', 'Tambah Riwayat & Arsip')
@section('banner_subtitle', 'Form pengarsipan data kegiatan atau dokumen baru.')

@section('banner_action')
    <a href="{{ route('riwayat.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Riwayat & Arsip
    </a>
@endsection

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Form Tambah Arsip</h4>
            <p class="text-muted small mb-0">Form pengarsipan data kegiatan atau dokumen baru.</p>
        </div>
    </div>

    <div class="glass-card-subtle p-5 rounded-4 text-center my-4">
        <div class="empty-state-icon mb-3">
            <i class="fas fa-folder-plus fa-2x"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Form Pengarsipan Data</h6>
        <p class="text-muted small mb-0">Halaman form penambahan data arsip dan histori siap digunakan.</p>
    </div>
</div>
@endsection
