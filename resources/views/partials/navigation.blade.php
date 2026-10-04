{{--
    Navigation Menu SIMONKA
    Sidebar navigation yang clean dan minimalis.
--}}
@php
    $isAgendaActive = request()->routeIs('agenda.*');
    $isKonsultasiActive = request()->routeIs('konsultasi.*') || request()->routeIs('penjadwalan-ulang.*');
@endphp

<ul class="navbar-nav iq-main-menu" id="sidebar-menu">

    {{-- ================= MENU UTAMA ================= --}}
    <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Menu Utama</span>
            <span class="mini-icon">-</span>
        </a>
    </li>

    {{-- 1. Dashboard --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="page" href="{{ route('dashboard') }}" wire:navigate>
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <path opacity="0.4" d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z" fill="currentColor"></path>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Dashboard</span>
        </a>
    </li>

    {{-- 2. Dropdown: Agenda --}}
    <li class="nav-item">
        <a class="nav-link {{ $isAgendaActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#sidebar-agenda" role="button" aria-expanded="{{ $isAgendaActive ? 'true' : 'false' }}" aria-controls="sidebar-agenda">
            <i class="icon">
                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M18.5 3H5.5C3.567 3 2 4.567 2 6.5V19.5C2 21.433 3.567 23 5.5 23H18.5C20.433 23 22 21.433 22 19.5V6.5C22 4.567 20.433 3 18.5 3Z" fill="currentColor"/>
                    <path d="M16 2V5M8 2V5M2 8.5H22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </i>
            <span class="item-name">Agenda</span>
            <i class="right-icon">
                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </i>
        </a>
        <ul class="sub-nav collapse {{ $isAgendaActive ? 'show' : '' }}" id="sidebar-agenda" data-bs-parent="#sidebar-menu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agenda.jadwal.*') ? 'active' : '' }}" href="{{ route('agenda.jadwal.index') }}" wire:navigate>
                    <i class="sidenav-mini-icon"> J </i>
                    <span class="item-name">Jadwal Resmi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agenda.kalender.*') ? 'active' : '' }}" href="{{ route('agenda.kalender.index') }}" wire:navigate>
                    <i class="sidenav-mini-icon"> K </i>
                    <span class="item-name">Kalender</span>
                </a>
            </li>
        </ul>
    </li>

    {{-- 3. Dropdown: Konsultasi --}}
    <li class="nav-item">
        <a class="nav-link {{ $isKonsultasiActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#sidebar-konsultasi" role="button" aria-expanded="{{ $isKonsultasiActive ? 'true' : 'false' }}" aria-controls="sidebar-konsultasi">
            <i class="icon">
                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M12 2C6.48 2 2 6.48 2 12C2 13.85 2.5 15.55 3.39 17L2 22L7.15 20.65C8.59 21.51 10.24 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2Z" fill="currentColor"/>
                    <path d="M8 12H8.01M12 12H12.01M16 12H16.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </i>
            <span class="item-name">Konsultasi</span>
            <i class="right-icon">
                <svg class="icon-18" xmlns="http://www.w3.org/2000/svg" width="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </i>
        </a>
        <ul class="sub-nav collapse {{ $isKonsultasiActive ? 'show' : '' }}" id="sidebar-konsultasi" data-bs-parent="#sidebar-menu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('konsultasi.index') || request()->routeIs('konsultasi.edit') ? 'active' : '' }}" href="{{ route('konsultasi.index') }}" wire:navigate>
                    <i class="sidenav-mini-icon"> D </i>
                    <span class="item-name">Data</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('konsultasi.create') ? 'active' : '' }}" href="{{ route('konsultasi.create') }}" wire:navigate>
                    <i class="sidenav-mini-icon"> T </i>
                    <span class="item-name">Tambah Baru</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('penjadwalan-ulang.*') ? 'active' : '' }}" href="{{ route('penjadwalan-ulang.index') }}" wire:navigate>
                    <i class="sidenav-mini-icon"> R </i>
                    <span class="item-name">Reschedule</span>
                </a>
            </li>
        </ul>
    </li>

    <li><hr class="hr-horizontal"></li>

    {{-- ================= ARSIP & LAPORAN ================= --}}
    <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Laporan</span>
            <span class="mini-icon">-</span>
        </a>
    </li>

    {{-- 4. Arsip --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}" href="{{ route('riwayat.index') }}" wire:navigate>
            <i class="icon">
                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M19 4H5C3.89543 4 3 4.89543 3 6V20C3 21.1046 3.89543 22 5 22H19C20.1046 22 21 21.1046 21 20V6C21 4.89543 20.1046 4 19 4Z" fill="currentColor"/>
                    <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M16 2V6M8 2V6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </i>
            <span class="item-name">Arsip</span>
        </a>
    </li>

    {{-- 5. Laporan --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}" wire:navigate>
            <i class="icon">
                <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path opacity="0.4" d="M16 2H8C6.89543 2 6 2.89543 6 4V8H18V4C18 2.89543 17.1046 2 16 2Z" fill="currentColor"/>
                    <path d="M6 18H18V14C18 12.8954 17.1046 12 16 12H8C6.89543 12 6 12.8954 6 14V18Z" fill="currentColor"/>
                    <path d="M19 8H5C3.89543 8 3 8.89543 3 10V16C3 17.1046 3.89543 18 5 18H6V14C6 12.8954 6.89543 12 8 12H16C17.1046 12 18 12.8954 18 14V18H19C20.1046 18 21 17.1046 21 16V10C21 8.89543 20.1046 8 19 8ZM18 11C17.4477 11 17 10.5523 17 10C17 9.44772 17.4477 9 18 9C18.5523 9 19 9.44772 19 10C19 10.5523 18.5523 11 18 11Z" fill="currentColor"/>
                </svg>
            </i>
            <span class="item-name">Cetak Laporan</span>
        </a>
    </li>

</ul>
