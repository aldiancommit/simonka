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

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js" data-navigate-once></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js" data-navigate-once></script>

<!-- App Script -->
<script src="{{ asset('assets/js/hope-ui.js') }}?v={{ filemtime(public_path('assets/js/hope-ui.js')) }}" data-navigate-once defer></script>

<!-- SIMONKA Modern Alert & Confirmation System -->
<script src="{{ asset('assets/js/simonka-alerts.js') }}?v={{ filemtime(public_path('assets/js/simonka-alerts.js')) }}" data-navigate-once defer></script>

@livewireScripts

<script data-navigate-once>
    let activeCalendar = null;

    const initializeNavigatedPage = () => {
        // 1. Sembunyikan loader jika masih terlihat setelah navigasi SPA
        const loader = document.getElementById('loading');
        if (loader) {
            loader.classList.add('d-none');
        }

        // Close mobile drawer on navigation
        document.body.classList.remove('sidebar-main');
        document.querySelector('.simonka-sidebar')?.classList.remove('sidebar-open');

        // 2. Scroll to top saat berganti halaman sebelum perhitungan offset
        window.scrollTo({ top: 0, behavior: 'instant' });

        // 3. Re-inisialisasi Bootstrap Tooltips & Popovers pada elemen baru
        if (window.bootstrap) {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
                bootstrap.Tooltip.getOrCreateInstance(el);
            });
        }

        // 4. Inisialisasi FullCalendar jika ada di halaman aktif
        const calendarElement = document.getElementById('calendar');
        if (calendarElement && window.FullCalendar) {
            if (activeCalendar) {
                activeCalendar.destroy();
                activeCalendar = null;
            }

            const eventsElement = document.getElementById('calendar-events');
            const events = eventsElement ? JSON.parse(eventsElement.textContent) : [];

            activeCalendar = new FullCalendar.Calendar(calendarElement, {
                initialView: 'dayGridMonth',
                locale: 'id',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                buttonIcons: false,
                buttonText: {
                    today: 'Hari ini',
                    month: 'Bulan',
                    week: 'Minggu',
                    day: 'Hari',
                    list: 'Agenda',
                    prev: '‹',
                    next: '›'
                },
                themeSystem: 'bootstrap5',
                events,
                eventClick(info) {
                    if (info.event.url) {
                        info.jsEvent.preventDefault();
                        Livewire.navigate(info.event.url);
                    }
                }
            });

            activeCalendar.render();
        }

        // 5. Refresh AOS jika ada elemen yang menggunakan data-aos
        if (window.AOS && document.querySelector('[data-aos]')) {
            window.AOS.refresh();
        }
    };

    document.addEventListener('DOMContentLoaded', initializeNavigatedPage);
    document.addEventListener('livewire:navigated', initializeNavigatedPage);

    document.addEventListener('livewire:navigating', () => {
        if (activeCalendar) {
            activeCalendar.destroy();
            activeCalendar = null;
        }
    });

    document.addEventListener('change', (event) => {
        if (event.target.matches('select[name="jenis"]')) {
            const form = event.target.closest('form');
            const filter = form?.querySelector('input[name="filter"]');

            if (form && filter) {
                filter.value = '0';
                form.submit();
            }
        }
    });
</script>

@stack('scripts')
