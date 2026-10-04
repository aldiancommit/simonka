@extends('layouts.admin')

@section('title', 'Hapus Konsultasi')
@section('banner_title', 'Hapus Data Konsultasi')
@section('banner_subtitle', 'Konfirmasi penghapusan data permohonan konsultasi.')

@section('banner_action')
    <a href="{{ route('konsultasi.index') }}" class="btn btn-light btn-sm" wire:navigate>
        Kembali ke Data Konsultasi
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="card border-danger">
            <div class="card-header bg-soft-danger d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title text-danger">Konfirmasi Hapus Data Konsultasi #{{ $id ?? '' }}</h4>
                </div>
            </div>
            <div class="card-body">
                {{-- Konfirmasi hapus konsultasi (Kosong / Siap Dikembangkan) --}}
                <div class="text-center py-4">
                    <p class="text-muted">Apakah Anda yakin ingin menghapus data permohonan konsultasi ini?</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
