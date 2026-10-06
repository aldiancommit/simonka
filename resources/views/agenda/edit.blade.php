@extends('layouts.admin')

@section('title', 'Edit Agenda')
@section('banner_title', 'Edit Agenda')
@section('banner_subtitle', 'Ubah data kegiatan agenda.')

@section('banner_action')
    <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-fundflow-glass btn-sm" wire:navigate>
        <i class="fas fa-arrow-left me-1"></i> Kembali ke Jadwal
    </a>
@endsection

@section('content')
<div class="glass-card p-4 p-md-5 simonka-fade-in">
    <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-white">
        <div>
            <h4 class="fw-bolder mb-1 text-dark tracking-tight">Form Edit Agenda</h4>
            <p class="text-muted small mb-0">Ubah dan perbarui data kegiatan agenda.</p>
        </div>
    </div>

    <div class="glass-card-subtle p-5 rounded-4 text-center my-4">
        <div class="empty-state-icon mb-3">
            <i class="fas fa-edit fa-2x"></i>
        </div>
        <h6 class="fw-bold text-dark mb-1">Form Edit Agenda</h6>
        <p class="text-muted small mb-0">Halaman form edit agenda siap dikembangkan.</p>
    </div>
</div>
@endsection
