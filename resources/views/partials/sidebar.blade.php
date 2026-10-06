<aside class="simonka-sidebar sidebar sidebar-default">
    <!-- Sidebar Brand & Toggle -->
    <div class="sidebar-header d-flex align-items-center justify-content-between px-3 py-3 border-bottom border-white">
        <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center gap-2.5 text-decoration-none overflow-hidden" wire:navigate>
                <img src="{{ asset('assets/images/logo-palu.png') }}" alt="Logo Kota Palu" style="max-height: 50px; max-width: 50px; object-fit: contain;">
            <div class="sidebar-brand-text">
                <h4 class="logo-title mb-0 fw-bolder tracking-tight" style="font-size: 1.15rem; letter-spacing: -0.02em; line-height: 1.2;">KESBANGPOL</h4>
            </div>
        </a>
    </div>

    <!-- Navigation Menu List -->
    <div class="sidebar-body py-2 px-1 flex-grow-1 overflow-y-auto">
        <div class="sidebar-list">
            @include('partials.navigation')
        </div>
    </div>

    <!-- Bottom Status Card -->
    <!-- <div class="sidebar-footer p-3 border-top border-white mt-auto">
        <div class="glass-card-subtle p-2.5 rounded-3 text-start d-flex align-items-center gap-2">
            <div class="rounded-circle bg-dark text-white p-1 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 26px; height: 26px; font-size: 0.7rem;">
                <i class="fas fa-check-circle text-success"></i>
            </div>
            <div class="overflow-hidden">
                <span class="d-block fw-bold text-dark lh-1 text-truncate" style="font-size: 0.74rem;">Sistem Online</span>
                <span class="text-muted" style="font-size: 0.65rem;">v2.0 FundFlow UI</span>
            </div>
        </div>
    </div> -->
</aside>
