<div class="card shadow-sm mb-4">
    <div class="card-header bg-gradient-primary text-white">
        <h5 class="mb-0 text-white">Tim & Paper Anda</h5>
    </div>
    <div class="card-body">
        @if ($teams->isEmpty())
            <p class="text-muted">Tidak ada tim atau paper yang ditemukan.</p>
        @else
            <div class="list-group">
                @foreach ($teams as $team)
                    <a href="{{ route('profile.showPaperDetail', ['teamId' => $team->id]) }}"
                        class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1">{{ $team->team_name }}</h5>
                            <span class="badge bg-info text-dark">{{ $team->status_lomba }}</span>
                        </div>
                        <small>Perusahaan: {{ $team->company_code }}</small>
                        @if ($team->paper)
                            <div class="mt-2">
                                <h6>Paper: {{ $team->paper->innovation_title }}</h6>
                                <p class="text-muted mb-1">{{ $team->paper->abstract }}</p>
                                <span class="badge {{ getStatusBadgeClass($team->paper->status) }}">Status Approval :
                                    {{ $team->paper->status }}</span>
                                <br>
                                @if ($team->pvtEventTeams->first())
                                    <span
                                        class="badge {{ getAssessmentStatusBadgeClass($team->pvtEventTeams->first()->status) }}">
                                        Status Penilaian : {{ $team->pvtEventTeams->first()->status }}
                                    </span>
                                @endif



                            </div>
                        @else
                            <p class="text-muted mt-2">Tidak ada paper yang diajukan untuk tim ini.</p>
                        @endif
                        @foreach ($team->pvtEventTeams as $eventTeam)
                            @if ($eventTeam->event)
                                <small class="d-block text-muted">Event:
                                    {{ $eventTeam->event->event_name }}</small>
                            @endif
                        @endforeach
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
