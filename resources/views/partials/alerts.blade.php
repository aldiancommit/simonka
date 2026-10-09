{{-- 
    SIMONKA - Global Modern Alert & Flash Data Carrier
    Seamlessly passes server-side flash notifications to the FundFlow glassmorphism toast & alert system.
--}}

@if(session('success'))
    <div class="d-none" data-simonka-flash data-flash-type="success" data-flash-title="Berhasil!" data-flash-message="{{ session('success') }}"></div>
@endif

@if(session('error'))
    <div class="d-none" data-simonka-flash data-flash-type="error" data-flash-title="Terjadi Kesalahan!" data-flash-message="{{ session('error') }}"></div>
@endif

@if(session('warning'))
    <div class="d-none" data-simonka-flash data-flash-type="warning" data-flash-title="Perhatian!" data-flash-message="{{ session('warning') }}"></div>
@endif

@if(session('status'))
    <div class="d-none" data-simonka-flash data-flash-type="info" data-flash-title="Informasi" data-flash-message="{{ session('status') }}"></div>
@endif

@if(session('info'))
    <div class="d-none" data-simonka-flash data-flash-type="info" data-flash-title="Informasi" data-flash-message="{{ session('info') }}"></div>
@endif

{{-- Optional inline alert if requested or if errors exist in global context --}}
@if(isset($inline) && $inline && session('success'))
    <div class="simonka-alert simonka-alert-success simonka-fade-in" role="alert">
        <div class="simonka-alert-icon">
            <i class="fas fa-check"></i>
        </div>
        <div class="simonka-alert-content">
            <div class="simonka-alert-title">Berhasil!</div>
            <div class="simonka-alert-message">{{ session('success') }}</div>
        </div>
        <button type="button" class="simonka-alert-close" onclick="this.closest('.simonka-alert').remove()" aria-label="Tutup">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if(isset($inline) && $inline && session('error'))
    <div class="simonka-alert simonka-alert-danger simonka-fade-in" role="alert">
        <div class="simonka-alert-icon">
            <i class="fas fa-exclamation"></i>
        </div>
        <div class="simonka-alert-content">
            <div class="simonka-alert-title">Terjadi Kesalahan!</div>
            <div class="simonka-alert-message">{{ session('error') }}</div>
        </div>
        <button type="button" class="simonka-alert-close" onclick="this.closest('.simonka-alert').remove()" aria-label="Tutup">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif
