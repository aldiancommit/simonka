@extends('layouts.admin')

@section('title', 'Kalender Agenda')
@section('banner_title', 'Kalender Agenda')
@section('banner_subtitle', 'Tampilan kalender terintegrasi seluruh agenda kegiatan.')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Kalender Kegiatan & Konsultasi</h4>
                    <div class="d-flex gap-3 text-sm font-size-12">
                        <div class="d-flex align-items-center gap-1">
                            <span class="bg-info rounded-circle d-inline-block" style="width: 10px; height: 10px;"></span> Terjadwal
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="bg-primary rounded-circle d-inline-block" style="width: 10px; height: 10px;"></span> Berlangsung
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="bg-success rounded-circle d-inline-block" style="width: 10px; height: 10px;"></span> Selesai
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="bg-warning rounded-circle d-inline-block" style="width: 10px; height: 10px;"></span> Konsultasi
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="calendar" style="min-height: 600px;"></div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            themeSystem: 'bootstrap5',
            events: {!! json_encode($events) !!},
            eventClick: function(info) {
                if(info.event.url) {
                    info.jsEvent.preventDefault(); // don't let the browser navigate
                    window.location.href = info.event.url;
                }
            }
        });
        calendar.render();
    });
</script>

<style>
    /* Clean up fullcalendar styles to fit Hope UI */
    .fc .fc-toolbar-title { font-size: 1.25rem; font-weight: 600; }
    .fc .fc-button-primary { background-color: #3a57e8; border-color: #3a57e8; }
    .fc .fc-button-primary:not(:disabled):active, .fc .fc-button-primary:not(:disabled).fc-button-active { background-color: #2b44c6; border-color: #2b44c6; }
    .fc-event { cursor: pointer; border: none; padding: 2px 4px; border-radius: 4px; font-size: 0.8rem; }
    .fc-daygrid-event-dot { display: none; }
    .fc-day-today { background-color: rgba(58, 87, 232, 0.05) !important; }
</style>
@endsection
