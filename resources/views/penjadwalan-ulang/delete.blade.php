@extends('layouts.admin')

@section('title', 'Hapus Penjadwalan Ulang')
@section('banner_title', 'Hapus Penjadwalan Ulang')
@section('banner_subtitle', 'Konfirmasi pembatalan/penghapusan pengajuan jadwal ulang.')

@section('banner_action')
    <a href="{{ route('penjadwalan-ulang.index') }}" class="btn btn-light btn-sm" wire:navigate>
        Kembali ke Daftar
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="card border-danger">
            <div class="card-header bg-soft-danger d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title text-danger">Konfirmasi Hapus Penjadwalan Ulang #{{ $id ?? '' }}</h4>
                </div>
            </div>
            <div class="card-body">
                {{-- Konfirmasi hapus jadwal ulang (Kosong / Siap Dikembangkan) --}}
                <div class="text-center py-4">
                    <p class="text-muted">Apakah Anda yakin ingin menghapus data penjadwalan ulang ini?</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
