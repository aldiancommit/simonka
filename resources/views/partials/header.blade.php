{{--
    SIMONKA - Unified Workspace Header
    FundFlow Design System: Single seamless header combining title context, quick actions, search, date pill, and profile.
--}}
<header class="simonka-header glass-card mb-4 p-3 p-md-4">
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
        <!-- Left Section: Title & Subtitle + Sidebar Toggle -->
        <div class="d-flex align-items-center gap-3">
            <div>
                <h1 class="simonka-page-title mb-0">@yield('banner_title', 'Dashboard SIMONKA')</h1>
                <p class="simonka-page-subtitle mb-0">@yield('banner_subtitle', 'Sistem Informasi Monitoring Konsultasi dan Agenda Kepala Badan.')</p>
            </div>
        </div>

        <!-- Right Section: Actions, Today's Date Pill, Profile Dropdown -->
        <div class="d-flex flex-wrap align-items-center justify-content-start justify-content-lg-end gap-2.5">
            @yield('banner_action')

            {{-- User Profile Pill & Dropdown --}}
            <div class="dropdown">
                <a class="nav-link py-2 px-3 glass-pill d-inline-flex align-items-center gap-2 text-decoration-none shadow-sm hover-top transition-all profile-trigger-btn" 
                href="#" 
                id="userDropdown" 
                role="button" 
                data-bs-toggle="dropdown" 
                aria-expanded="false">
                    
                    <!-- Avatar -->
                    <div class="rounded-circle overflow-hidden border border-2 border-white flex-shrink-0" style="width: 36px; height: 36px;">
                        <img src="{{ asset('assets/images/avatars/01.png') }}" 
                            alt="User Profile" 
                            class="img-fluid w-100 h-100 object-fit-cover" 
                            onerror="this.src='https://placehold.co/80x80/111827/white?text=U'">
                    </div>

                    <!-- Nama / Role -->
                    <div class="d-none d-sm-block text-start">
                        <span class="d-block fw-bold text-dark lh-1" style="font-size: 0.85rem;">{{ auth()->user()?->name ?? 'Profile' }}</span>
                    </div>

                    <!-- Panah Indikator -->
                    <i class="fas fa-chevron-down text-muted" style="font-size: 0.65rem;"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end simonka-profile-dropdown shadow-lg border-0 p-2 mt-2" 
                aria-labelledby="userDropdown"
                style="min-width: 250px; background-color: #ffffff !important; opacity: 1 !important; z-index: 99999 !important; position: absolute !important; pointer-events: auto !important; border: 1px solid rgba(0,0,0,0.1) !important; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;">
    
                    <!-- Header Profil -->
                    <li class="px-2 py-2 mb-1" style="border-bottom: 1px solid #f1f5f9;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle overflow-hidden shadow-xs border border-light flex-shrink-0 me-2" style="width: 38px; height: 38px;">
                                <img src="{{ asset('assets/images/avatars/01.png') }}" 
                                    alt="User Profile" 
                                    class="img-fluid w-100 h-100 object-fit-cover" 
                                    onerror="this.src='https://placehold.co/80x80/111827/white?text=U'">
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="fw-bold text-dark mb-0 lh-1" style="font-size: 0.9rem;">{{ auth()->user()?->name ?? 'Pengguna' }}</h6>
                                @if(auth()->user()?->role)
                                    <span class="badge bg-soft-primary text-primary mt-1 small" style="width: fit-content;">{{ auth()->user()->role->label() }}</span>
                                @endif
                            </div>
                        </div>
                    </li>

                    <!-- Item Menu: Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}" 
                        class="dropdown-item rounded-2 py-2 px-3 fw-semibold text-dark d-flex align-items-center gap-2.5" 
                        wire:navigate>
                            <i class="fas fa-chart-pie text-primary fa-fw"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Item Menu: Arsip Aktivitas -->
                    <li>
                        <a href="{{ route('riwayat.index') }}" 
                        class="dropdown-item rounded-2 py-2 px-3 fw-semibold text-dark d-flex align-items-center gap-2.5" 
                        wire:navigate>
                            <i class="fas fa-history text-secondary fa-fw"></i>
                            <span>Arsip Aktivitas</span>
                        </a>
                    </li>

                    <li><hr class="dropdown-divider my-1 opacity-25"></li>

                    <!-- Item Menu: Logout (Native POST form with CSRF) -->
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item rounded-2 py-2 px-3 fw-semibold text-danger d-flex align-items-center gap-2.5 w-100 border-0 bg-transparent text-start">
                                <i class="fas fa-sign-out-alt text-danger fa-fw"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</header>
