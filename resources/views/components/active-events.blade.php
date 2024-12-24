<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-white">
        <h5 class="mb-0">Jadwal Event Aktif</h5>
    </div>
    <div class="card-body">
        @if($activeEvents->isEmpty())
            <p class="text-muted">Tidak ada event aktif yang sedang diikuti.</p>
        @else
            <div class="list-group">
                @foreach($activeEvents as $event)
                    <a href="#" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $event->event_name }}</h5>
                            <small>{{ $event->date_start }} - {{ $event->date_end }}</small>
                        </div>
                        <p class="mb-1">{{ $event->description }}</p>
                        <small class="text-muted">Status: {{ $event->status }}</small>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
