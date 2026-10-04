@extends('layouts.admin')

@section('title', 'Tambah Arsip')
@section('banner_title', 'Tambah Riwayat & Arsip')
@section('banner_subtitle', 'Form pengarsipan data kegiatan atau dokumen baru.')

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
                    <h4 class="card-title">Form Tambah Arsip</h4>
                </div>
            </div>
            <div class="card-body">
                {{-- Form tambah arsip (Kosong / Siap Dikembangkan) --}}
                <div class="text-center py-5">
                    <p class="text-muted mb-0">Halaman form penambahan data arsip/riwayat.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
