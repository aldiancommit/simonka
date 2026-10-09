@extends('layouts.admin')

@section('title', 'Kalender Agenda')
@section('banner_title', 'Kalender Agenda')
@section('banner_subtitle', 'Tampilan kalender terintegrasi seluruh agenda kegiatan resmi dan konsultasi pimpinan.')

@section('banner_action')
    @can('create', App\Models\JadwalKegiatan::class)
        <a href="{{ route('agenda.jadwal.create') }}" class="btn btn-fundflow-primary" wire:navigate>
            <i class="fas fa-plus"></i> Tambah Jadwal Baru
        </a>
    @endcan
@endsection

@section('content')
<div class="row simonka-fade-in">
    <div class="col-12">
        <div class="glass-card p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 pb-2 border-bottom border-white">
                <div>
                    <h4 class="fw-bolder mb-1 text-dark tracking-tight">Kalender Terpadu</h4>
                    <p class="text-muted small mb-0">Klik pada nama agenda untuk meninjau rincian kegiatan</p>
                </div>
                <div class="d-flex gap-2 text-sm flex-wrap">
                    <span class="badge bg-soft-info">Terjadwal</span>
                    <span class="badge bg-soft-primary"> Berlangsung</span>
                    <span class="badge bg-soft-success"> Selesai</span>
                    <span class="badge bg-soft-warning"> Konsultasi</span>
                </div>
            </div>
            
            <div id="calendar" style="min-height: 640px;"></div>
        </div>
    </div>
</div>

<script type="application/json" id="calendar-events">@json($events)</script>

<style>
    /* FundFlow FullCalendar Styling */
    .fc .fc-toolbar { 
        display: flex; 
        flex-wrap: wrap; 
        align-items: center; 
        justify-content: space-between; 
        gap: 1rem; 
        margin-bottom: 1.5rem !important; 
    }
    .fc .fc-toolbar-chunk { 
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
        flex-wrap: wrap; 
    }
    .fc .fc-button-group { 
        display: flex !important; 
        gap: 0.45rem !important; 
    }
    .fc .fc-button-group > .fc-button { 
        border-radius: 9999px !important; 
        margin: 0 !important; 
    }
    .fc .fc-toolbar-title { 
        font-size: 1.35rem; 
        font-weight: 800; 
        color: #0f172a; 
        letter-spacing: -0.02em; 
    }

    /* Left Toolbar Buttons: Prev (←), Next (→), and Hari ini (btn-fundflow-glass) */
    .fc .fc-prev-button,
    .fc .fc-next-button {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border: 1px solid rgba(226, 232, 240, 0.95) !important;
        color: #1e293b !important;
        border-radius: 9999px !important;
        font-weight: 700 !important;
        font-size: 1.05rem !important;
        line-height: 1 !important;
        padding: 0.45rem 0.95rem !important;
        min-width: 38px !important;
        height: 38px !important;
        box-shadow: 0 2px 8px rgba(79, 93, 128, 0.06) !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    .fc .fc-today-button {
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(12px) !important;
        -webkit-backdrop-filter: blur(12px) !important;
        border: 1px solid rgba(226, 232, 240, 0.95) !important;
        color: #1e293b !important;
        border-radius: 9999px !important;
        font-weight: 600 !important;
        font-size: 0.8125rem !important;
        padding: 0.45rem 1.15rem !important;
        height: 38px !important;
        box-shadow: 0 2px 8px rgba(79, 93, 128, 0.06) !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-transform: none !important;
    }

    .fc .fc-prev-button:hover,
    .fc .fc-next-button:hover,
    .fc .fc-today-button:hover {
        background: #ffffff !important;
        color: #0f172a !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(79, 93, 128, 0.12) !important;
    }

    .fc .fc-today-button:disabled {
        opacity: 0.65 !important;
        cursor: not-allowed !important;
        transform: none !important;
    }

    /* Right Toolbar Buttons: Bulan, Minggu, Hari, Agenda (btn-fundflow-primary) */
    .fc .fc-dayGridMonth-button,
    .fc .fc-timeGridWeek-button,
    .fc .fc-timeGridDay-button,
    .fc .fc-listWeek-button {
        background: #111827 !important;
        border: 1px solid #111827 !important;
        color: #ffffff !important;
        border-radius: 9999px !important;
        font-weight: 600 !important;
        font-size: 0.8125rem !important;
        padding: 0.45rem 1.15rem !important;
        height: 38px !important;
        box-shadow: 0 4px 12px rgba(17, 24, 39, 0.15) !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        text-transform: none !important;
    }

    .fc .fc-dayGridMonth-button:hover,
    .fc .fc-timeGridWeek-button:hover,
    .fc .fc-timeGridDay-button:hover,
    .fc .fc-listWeek-button:hover {
        background: #000000 !important;
        border-color: #000000 !important;
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2) !important;
    }

    .fc .fc-dayGridMonth-button.fc-button-active,
    .fc .fc-timeGridWeek-button.fc-button-active,
    .fc .fc-timeGridDay-button.fc-button-active,
    .fc .fc-listWeek-button.fc-button-active {
        background: #3b82f6 !important;
        border-color: #3b82f6 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.35) !important;
    }

    /* Table Grid Styling */
    .fc-theme-bootstrap5 th { 
        font-weight: 700 !important; 
        font-size: 0.72rem !important; 
        text-transform: uppercase !important; 
        letter-spacing: 0.05em !important; 
        color: #64748b !important;
        background-color: rgba(255, 255, 255, 0.4) !important; 
        padding: 0.75rem 0.5rem !important;
        border-color: rgba(255, 255, 255, 0.6) !important;
    }
    .fc-theme-bootstrap5 td {
        border-color: rgba(255, 255, 255, 0.5) !important;
    }
    .fc-daygrid-day-number {
        font-weight: 600;
        font-size: 0.82rem;
        color: #475569;
        padding: 0.4rem !important;
    }
    .fc-day-today { 
        background-color: rgba(59, 130, 246, 0.08) !important; 
    }
    .fc-event { 
        cursor: pointer; 
        border: none !important; 
        padding: 4px 8px !important; 
        border-radius: 10px !important; 
        font-size: 0.78rem !important; 
        font-weight: 600 !important; 
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08) !important; 
        transition: transform 0.15s ease !important;
    }
    .fc-event:hover {
        transform: scale(1.02);
    }
    .fc-daygrid-event-dot { display: none; }
</style>
@endsection
