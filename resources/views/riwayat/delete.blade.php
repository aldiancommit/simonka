@extends('layouts.admin')

@section('title', 'Hapus Arsip')
@section('banner_title', 'Hapus Riwayat & Arsip')
@section('banner_subtitle', 'Konfirmasi penghapusan data arsip.')

@section('banner_action')
    <a href="{{ route('riwayat.index') }}" class="btn btn-light btn-sm" wire:navigate>
        Kembali ke Riwayat & Arsip
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="card border-danger">
            <div class="card-header bg-soft-danger d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title text-danger">Konfirmasi Hapus Arsip #{{ $id ?? '' }}</h4>
                </div>
            </div>
            <div class="card-body">
                {{-- Konfirmasi hapus arsip (Kosong / Siap Dikembangkan) --}}
                <div class="text-center py-4">
                    <p class="text-muted">Apakah Anda yakin ingin menghapus data arsip ini?</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
