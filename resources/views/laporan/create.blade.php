@extends('layouts.admin')

@section('title', 'Buat Laporan Baru')
@section('banner_title', 'Buat Laporan Baru')
@section('banner_subtitle', 'Konfigurasi parameter pembuatan dokumen laporan baru.')

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
                    <h4 class="card-title">Form Parameter Laporan</h4>
                </div>
            </div>
            <div class="card-body">
                {{-- Form buat laporan baru (Kosong / Siap Dikembangkan) --}}
                <div class="text-center py-5">
                    <p class="text-muted mb-0">Halaman form pembuatan parameter laporan baru.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
