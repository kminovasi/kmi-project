<div class="card shadow-sm mb-4">
    <div class="card-header bg-gradient bg-primary text-white">
        <h5 class="mb-0 text-white">Tim & Paper Anda</h5>
    </div>
    <div class="card-body">
        @if ($teams->isEmpty())
            <p class="text-muted">Tidak ada tim atau paper yang ditemukan.</p>
        @else
            <div class="list-group">
                @foreach ($teams as $team)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            {{-- Link utama untuk ke detail --}}
                            <div>
                                <h5 class="mb-1">{{ $team->team_name }}</h5>
                                <small>Kode Perusahaan: {{ $team->company_code }}</small>
                                <div>
                                    <span class="badge bg-info text-dark text-capitalize">{{ $team->status_lomba }}</span>
                                </div>
                            </div>
            
                            {{-- Tombol collapse --}}
                            <button 
                                class="btn btn-sm btn-outline-primary"
                                type="button"
                                data-bs-toggle="collapse" 
                                data-bs-target="#collapseTeam{{ $team->id }}"
                                aria-expanded="false"
                                aria-controls="collapseTeam{{ $team->id }}"
                            >
                                Detail
                            </button>
                        </div>
            
                        {{-- Konten collapsible --}}
                        <a href="{{ route('profile.showPaperDetail', ['teamId' => $team->id]) }}" class="text-decoration-none">
                            <div class="collapse mt-2" id="collapseTeam{{ $team->id }}">
                                <div class="border-top pt-2">
                                    @if ($team->paper)
                                        <h6>Paper: {{ $team->paper->innovation_title }}</h6>
                                        <p class="text-muted mb-1">{{ $team->paper->abstract }}</p>
                                        <span class="badge mb-2 {{ getStatusBadgeClass($team->paper->status) }}">
                                            Status Approval: {{ $team->paper->status }}
                                        </span>
                                        <br>
                                        @if ($team->pvtEventTeams->first())
                                            <span class="badge mb-2 {{ getAssessmentStatusBadgeClass($team->pvtEventTeams->first()->status) }}">
                                                Status Penilaian: {{ $team->pvtEventTeams->first()->status }}
                                            </span>
                                        @endif
                                    @else
                                        <p class="text-muted">Tidak ada paper yang diajukan untuk tim ini.</p>
                                    @endif
                
                                    @foreach ($team->pvtEventTeams as $eventTeam)
                                        @if ($eventTeam->event)
                                            <small class="d-block text-muted mb-2">
                                                Event: {{ $eventTeam->event->event_name . ' Tahun ' . $eventTeam->event->year }}
                                            </small>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
