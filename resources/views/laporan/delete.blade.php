@extends('layouts.admin')

@section('title', 'Hapus Laporan')
@section('banner_title', 'Hapus Dokumen Laporan')
@section('banner_subtitle', 'Konfirmasi penghapusan data atau berkas laporan.')

@section('banner_action')
    <a href="{{ route('laporan.index') }}" class="btn btn-light btn-sm" wire:navigate>
        Kembali ke Laporan
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="card border-danger">
            <div class="card-header bg-soft-danger d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title text-danger">Konfirmasi Hapus Laporan #{{ $id ?? '' }}</h4>
                </div>
            </div>
            <div class="card-body">
                {{-- Konfirmasi hapus laporan (Kosong / Siap Dikembangkan) --}}
                <div class="text-center py-4">
                    <p class="text-muted">Apakah Anda yakin ingin menghapus arsip/dokumen laporan ini?</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
