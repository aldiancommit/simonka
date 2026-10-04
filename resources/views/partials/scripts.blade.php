<!-- Library Bundle Script -->
<script src="{{ asset('assets/js/core/libs.min.js') }}" data-navigate-once></script>

<!-- External Library Bundle Script -->
<script src="{{ asset('assets/js/core/external.min.js') }}" data-navigate-once></script>

<!-- Widgetchart Script -->
<script src="{{ asset('assets/js/charts/widgetcharts.js') }}" data-navigate-once></script>

<!-- mapchart Script -->
<script src="{{ asset('assets/js/charts/vectore-chart.js') }}" data-navigate-once></script>
<script src="{{ asset('assets/js/charts/dashboard.js') }}" data-navigate-once></script>

<!-- fslightbox Script -->
<script src="{{ asset('assets/js/plugins/fslightbox.js') }}" data-navigate-once></script>

<!-- Settings Script -->
<script src="{{ asset('assets/js/plugins/setting.js') }}" data-navigate-once></script>

<!-- Slider-tab Script -->
<script src="{{ asset('assets/js/plugins/slider-tabs.js') }}" data-navigate-once></script>

<!-- Form Wizard Script -->
<script src="{{ asset('assets/js/plugins/form-wizard.js') }}" data-navigate-once></script>

<!-- AOS Animation Plugin-->
<script src="{{ asset('assets/vendor/aos/dist/aos.js') }}" data-navigate-once></script>

<!-- App Script -->
<script src="{{ asset('assets/js/hope-ui.js') }}" data-navigate-once defer></script>

@livewireScripts

<script data-navigate-once>
    document.addEventListener('livewire:navigated', () => {
        // Sembunyikan loader jika masih terlihat setelah navigasi SPA
        const loader = document.getElementById('loading');
        if (loader) {
            loader.classList.add('d-none');
        }

        // Scroll to top saat berganti halaman
        window.scrollTo({ top: 0, behavior: 'instant' });
    });
</script>

@stack('scripts')
