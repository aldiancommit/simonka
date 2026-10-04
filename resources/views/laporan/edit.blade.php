@extends('layouts.admin')

@section('title', 'Edit Template / Laporan')
@section('banner_title', 'Edit Template / Laporan')
@section('banner_subtitle', 'Pembaruan data atau konfigurasi laporan.')

@section('banner_action')
    <a href="{{ route('laporan.index') }}" class="btn btn-light btn-sm" wire:navigate>
        Kembali ke Laporan
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Form Edit Laporan #{{ $id ?? '' }}</h4>
                </div>
            </div>
            <div class="card-body">
                {{-- Form edit laporan (Kosong / Siap Dikembangkan) --}}
                <div class="text-center py-5">
                    <p class="text-muted mb-0">Halaman form edit data konfigurasi laporan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
