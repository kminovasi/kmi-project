<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">Jadwal Event Aktif</h5>
    </div>
    <div class="card-body">
        @if($activeEvents->isEmpty())
            <p class="text-muted">Tidak ada event aktif yang sedang diikuti.</p>
        @else
            <ul class="timeline">
                @foreach($activeEvents as $event)
                    @foreach($event->timelines as $timeline)
                        <li class="timeline-item">
                            <h5 class="timeline-title">{{ $timeline->judul_kegiatan }}</h5>
                            <p class="timeline-date">{{ $timeline->tanggal_mulai }} - {{ $timeline->tanggal_selesai }}</p>
                            <p class="timeline-description">{{ $timeline->deskripsi }}</p>
                        </li>
                    @endforeach
                @endforeach
            </ul>
        @endif
    </div>
</div>

<style>
    .timeline {
        list-style: none;
        padding: 0;
        position: relative;
    }

    .timeline::before {
        content: '';
        background: #d4d9df;
        display: inline-block;
        position: absolute;
        left: 29px;
        width: 2px;
        height: 100%;
        z-index: 400;
    }

    .timeline-item {
        margin: 20px 0;
        padding-left: 50px;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        background: white;
        border: 3px solid #22c0e8;
        display: inline-block;
        position: absolute;
        border-radius: 50%;
        left: 20px;
        width: 20px;
        height: 20px;
        z-index: 400;
    }

    .timeline-title {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .timeline-date {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .timeline-description {
        margin: 10px 0;
    }
</style>
