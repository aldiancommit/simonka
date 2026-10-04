@extends('layouts.admin')

@section('title', 'Edit Riwayat & Arsip')
@section('banner_title', 'Edit Riwayat & Arsip')
@section('banner_subtitle', 'Form pembaruan informasi data arsip/histori.')

@section('banner_action')
    <a href="{{ route('riwayat.index') }}" class="btn btn-light btn-sm" wire:navigate>
        Kembali ke Riwayat & Arsip
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="header-title">
                    <h4 class="card-title">Form Edit Arsip #{{ $id ?? '' }}</h4>
                </div>
            </div>
            <div class="card-body">
                {{-- Form edit arsip (Kosong / Siap Dikembangkan) --}}
                <div class="text-center py-5">
                    <p class="text-muted mb-0">Halaman form edit data riwayat & arsip.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
