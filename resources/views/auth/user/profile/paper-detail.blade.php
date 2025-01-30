@extends('layouts.app')
@section('title', 'Paper Detail')
@section('content')
    <x-header-content :title="'Detail Makalah dan Tim : ' . $team->team_name">
        <a href="{{ route('profile.index') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </x-header-content>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Informasi Tim</div>
                    <div class="card-body">
                        <p><strong>Nama Tim:</strong> {{ $team->team_name }}</p>
                        <p><strong>Perusahaan:</strong> {{ $team->company->company_name }}</p>
                        <p><strong>Status Lomba:</strong> <span class="badge bg-info">{{ $team->status_lomba }}</span></p>
                        <button class="btn btn-dark btn-xs" type="button" data-bs-toggle="modal"
                            data-bs-target="#detailTeamMember" onclick="get_data_on_modal({{ $team->id }})">
                            Detail
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">Informasi Makalah</div>
                    <div class="card-body">
                        @if ($team->paper)
                            <p><strong>Judul Inovasi:</strong> {{ $team->paper->innovation_title }}</p>
                            <p><strong>Abstrak:</strong> {{ $team->paper->abstract }}</p>
                            <p><strong>Status:</strong> <span class="badge bg-warning">Status Approval :
                                    {{ $team->paper->status }}</span></p>
                            <a href="{{ asset(Storage::url(mb_substr($team->paper->full_paper, 3))) }}"
                                class="btn btn-sm text-white" style="background-color: #e84637" target="_blank">
                                Lihat Makalah
                            </a>
                        @else
                            <p class="text-muted">Tidak ada makalah yang diajukan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if ($team->pvtEventTeams->isNotEmpty())
            @foreach ($team->pvtEventTeams as $eventTeam)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-header bg-danger text-white">Event:
                                {{ $eventTeam->event->event_name ?? 'N/A' }}</div>
                            <div class="card-body">
                                <p><strong>Status Penilaian:</strong>
                                    <span
                                        class="badge {{ getAssessmentStatusBadgeClass($eventTeam->status) }}">{{ $eventTeam->status }}</span>
                                </p>
                                <p><strong>Skor Desk:</strong> {{ $eventTeam->total_score_on_desk ?? 'N/A' }}</p>
                                <p><strong>Skor Presentasi:</strong> {{ $eventTeam->total_score_presentation ?? 'N/A' }}
                                </p>
                                <p><strong>Skor Kaukus:</strong> {{ $eventTeam->total_score_caucus ?? 'N/A' }}</p>
                                <p><strong>Skor Akhir:</strong> {{ $eventTeam->final_score ?? 'N/A' }}</p>

                                @if ($eventTeam->sofi)
                                    <hr>
                                    <h6 class="text-secondary">Informasi SOFI Penilaian</h6>
                                    <p><strong>Strength:</strong> {{ $eventTeam->sofi->strength }}</p>
                                    <p><strong>Opportunity for Improvement:</strong>
                                        {{ $eventTeam->sofi->opportunity_for_improvement }}</p>
                                    <a class="btn btn-sm btn-primary text-white"
                                        href="{{ route('assessment.download.sofi.oda', $eventTeam->id) }}" target="_blank">
                                        <i class="me-1" data-feather="download"></i>
                                        Download SOFI
                                    </a>
                                    <button class="btn btn-purple btn-sm" type="button" data-bs-toggle="modal"
                                        data-bs-target="#uploadStep"
                                        onclick="change_url_step({{ $team->paper->id }}, {{ $eventTeam->id }})">Revisi
                                        Makalah</button>
                                @else
                                    <p class="text-muted">Belum ada SOFI penilaian.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-muted">Tidak ada informasi event yang diikuti.</p>
        @endif
    </div>

    <x-profile.detail-team-modal :team="$team" />
    <div class="modal fade" id="uploadStep" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="uploadDocumentTitle">Unggah Dokumen</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="uploadStepFormProfilePaper" class="upload-step-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            {{-- <p id="paper_id_input"></p> --}}
                            <input type="hidden" name="paper_id" id="paper_id_input" value="">
                            <input type="hidden" name="pvt_event_team_id" id="pvt_event_team_id" value="">
                            <input type="file" name="file_stage" class="form-control" multiple accept=".pdf">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary btn-upload-step" type="submit"
                            data-bs-dismiss="modal">Kirim</button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('js')
        <script>
            function change_url_step(id, event_team_id) {
                var form = document.getElementById("uploadStepFormProfilePaper");
                var pvt_event_team_id_element = document.getElementById("pvt_event_team_id");
                var url = `{{ route('profile.showPaperDetail.paper-revision', ['teamId' => ':id']) }}`;
                url = url.replace(':id', id);
                form.action = url;
                pvt_event_team_id_element.value = event_team_id;
            }
        </script>
    @endpush
@endsection
