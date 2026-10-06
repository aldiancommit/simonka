{{--
    Navigation Menu SIMONKA
    FundFlow Clean Glass Navigation
--}}
@php
    $isAgendaActive = request()->routeIs('agenda.*');
    $isKonsultasiActive = request()->routeIs('konsultasi.*') || request()->routeIs('penjadwalan-ulang.*');
@endphp

<ul class="navbar-nav iq-main-menu" id="sidebar-menu">

    {{-- ================= MENU UTAMA ================= --}}
    <li class="nav-item static-item mb-1">
        <a class="nav-link static-item disabled p-0" href="#" tabindex="-1">
            <span class="default-icon">Menu Utama</span>
            <span class="mini-icon">-</span>
        </a>
    </li>

    {{-- 1. Dashboard --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="page" href="{{ route('dashboard') }}" wire:navigate>
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="9" rx="2" />
                    <rect x="14" y="3" width="7" height="5" rx="2" />
                    <rect x="14" y="12" width="7" height="9" rx="2" />
                    <rect x="3" y="16" width="7" height="5" rx="2" />
                </svg>
            </i>
            <span class="item-name">Dashboard</span>
        </a>
    </li>

    {{-- 2. Dropdown: Konsultasi --}}
    <li class="nav-item">
        <a class="nav-link {{ $isKonsultasiActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#sidebar-konsultasi" role="button" aria-expanded="{{ $isKonsultasiActive ? 'true' : 'false' }}" aria-controls="sidebar-konsultasi">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    <path d="M8 10h.01M12 10h.01M16 10h.01"></path>
                </svg>
            </i>
            <span class="item-name">Konsultasi</span>
            <i class="right-icon ms-auto">
                <svg class="icon-16" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </i>
        </a>
        <ul class="sub-nav collapse {{ $isKonsultasiActive ? 'show' : '' }}" id="sidebar-konsultasi" data-bs-parent="#sidebar-menu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('konsultasi.index') || request()->routeIs('konsultasi.show') || request()->routeIs('konsultasi.edit') ? 'active' : '' }}"
                   href="{{ route('konsultasi.index') }}"
                   wire:navigate>
                    <span class="item-name">Daftar Konsultasi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('penjadwalan-ulang.*') ? 'active' : '' }}"
                   href="{{ route('penjadwalan-ulang.index') }}"
                   wire:navigate>
                    <span class="item-name">Reschedule</span>
                </a>
            </li>
        </ul>
    </li>

    {{-- 3. Dropdown: Agenda --}}
    <li class="nav-item">
        <a class="nav-link {{ $isAgendaActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#sidebar-agenda" role="button" aria-expanded="{{ $isAgendaActive ? 'true' : 'false' }}" aria-controls="sidebar-agenda">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
            </i>
            <span class="item-name">Agenda</span>
            <i class="right-icon ms-auto">
                <svg class="icon-16" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </i>
        </a>
        <ul class="sub-nav collapse {{ $isAgendaActive ? 'show' : '' }}" id="sidebar-agenda" data-bs-parent="#sidebar-menu">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agenda.jadwal.*') ? 'active' : '' }}"
                   href="{{ route('agenda.jadwal.index') }}"
                   wire:navigate>
                    <span class="item-name">Jadwal Resmi</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('agenda.kalender.*') ? 'active' : '' }}"
                   href="{{ route('agenda.kalender.index') }}"
                   wire:navigate>
                    <span class="item-name">Kalender Agenda</span>
                </a>
            </li>
        </ul>
    </li>

    <li><hr class="hr-horizontal my-2"></li>

    {{-- ================= ARSIP & LAPORAN ================= --}}
    <li class="nav-item static-item mb-1">
        <a class="nav-link static-item disabled p-0" href="#" tabindex="-1">
            <span class="default-icon">Rekapitulasi</span>
            <span class="mini-icon">-</span>
        </a>
    </li>

    {{-- 4. Arsip --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}" href="{{ route('riwayat.index') }}" wire:navigate>
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="12 8 12 12 14 14"></polyline>
                    <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5"></path>
                </svg>
            </i>
            <span class="item-name">Arsip Riwayat</span>
        </a>
    </li>

    {{-- 5. Laporan --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}" wire:navigate>
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </i>
            <span class="item-name">Cetak Laporan</span>
        </a>
    </li>

</ul>
