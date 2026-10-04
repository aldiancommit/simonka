@extends('layouts.admin')

@section('title', 'Agenda')
@section('banner_title', 'Agenda Kepala Badan')
@section('banner_subtitle', 'Kelola jadwal kegiatan dan agenda Kepala Badan.')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <svg class="icon-40 text-primary" width="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M18.5 3H5.5C3.567 3 2 4.567 2 6.5V19.5C2 21.433 3.567 23 5.5 23H18.5C20.433 23 22 21.433 22 19.5V6.5C22 4.567 20.433 3 18.5 3Z" fill="currentColor"/>
                        <path d="M16 2V5M8 2V5M2 8.5H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h5>Jadwal Resmi</h5>
                <p class="text-muted mb-3">Lihat dan kelola jadwal kegiatan resmi.</p>
                <a href="{{ route('agenda.jadwal.index') }}" class="btn btn-primary btn-sm" wire:navigate>Buka Jadwal</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <svg class="icon-40 text-info" width="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M18.5 3H5.5C3.567 3 2 4.567 2 6.5V19.5C2 21.433 3.567 23 5.5 23H18.5C20.433 23 22 21.433 22 19.5V6.5C22 4.567 20.433 3 18.5 3Z" fill="currentColor"/>
                        <path d="M16 2V5M8 2V5M2 8.5H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <h5>Kalender</h5>
                <p class="text-muted mb-3">Tampilan kalender seluruh agenda.</p>
                <a href="{{ route('agenda.kalender.index') }}" class="btn btn-info btn-sm" wire:navigate>Buka Kalender</a>
            </div>
        </div>
    </div>
</div>
@endsection
