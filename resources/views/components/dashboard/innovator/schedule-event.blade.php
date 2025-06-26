<div class="card shadow-sm mb-4">
    <div class="card-header bg-gradient bg-primary text-white text-center">
        <h5 class="mb-0 font-weight-bold text-white">Jadwal Event Aktif</h5>
    </div>
    <div class="card-body">
        @if ($activeEvents->isEmpty())
            <p class="text-center text-muted">Tidak ada event aktif yang sedang diikuti.</p>
        @else
            <ul class="timeline">
                @foreach ($activeEvents as $event)
                    @foreach ($event->timelines as $timeline)
                        <li class="timeline-item">
                            <div class="timeline-content">
                                <h5 class="timeline-title">{{ $timeline->judul_kegiatan }}</h5>
                                <p class="timeline-date">
                                    <i class="fas fa-calendar-alt"></i>
                                    {{ $timeline->tanggal_mulai }} - {{ $timeline->tanggal_selesai }}
                                </p>
                                <p class="timeline-description">{{ $timeline->deskripsi }}</p>
                            </div>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        @endif
    </div>
</div>

<style>
    .card-header {
        font-size: 1.25rem;
        padding: 0.75rem 1rem;
    }

    .timeline {
        list-style: none;
        padding: 0;
        position: relative;
        margin: 0;
    }

    .timeline::before {
        content: '';
        background: #dee2e6;
        position: absolute;
        left: 30px;
        width: 3px;
        height: 100%;
        z-index: 1;
    }

    .timeline-item {
        margin: 20px 0;
        padding-left: 60px;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        background: #fff;
        border: 4px solid #22c0e8;
        position: absolute;
        left: 18px;
        top: 10px;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        z-index: 2;
    }

    .timeline-content {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .timeline-title {
        font-size: 1.125rem;
        font-weight: bold;
        color: #343a40;
        margin-bottom: 5px;
    }

    .timeline-date {
        font-size: 0.875rem;
        color: #6c757d;
        margin-bottom: 10px;
    }

    .timeline-description {
        font-size: 1rem;
        color: #495057;
    }
</style>
