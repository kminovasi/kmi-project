<style>
.innovation-card {
    background-color: #f8f9fa;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    color: #1D1616;
}

.innovation-label {
    font-size: 1.2rem;
    margin-right: 10px;
    font-weight: 600;
}

.innovation-value {
    font-size: 1.8rem;
    font-weight: 600;
    color: #D84040;
}

.innovation-completion {
    display: flex;
    justify-content: space-around;
    margin-top: 10px;
}

.completion-stat {
    text-align: center;
    padding: 10px 15px;
    border-radius: 6px;
    cursor: pointer;
    background-color: rgba(216, 64, 64, .1);
}

.completion-value {
    font-size: 1.4rem;
    font-weight: 600;
    color: #D84040;
    /* Warna primer */
}

.completion-label {
    font-size: 0.9rem;
    color: #1D1616;
}

.modal-body {
    overflow-y: auto;
    max-height: 60vh;
}

button[data-bs-toggle="collapse"] {
    display: block;
    width: 100%;
    box-sizing: border-box;
}

</style>
<div class="col-lg-5 col-xl-5 mb-8 mx-auto">
    <div class="bg-gradient-green text-white h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Informasi Teks -->
                <div class="me-3 flex-grow-1 d-flex flex-column gap-y-2">
                    <div class="row">
                        <div class="innovation-card">
                            <div class="innovation-stat d-flex justify-content-center align-items-center">
                                <span class="innovation-label">Total Team Terverifikasi</span>
                                <span class="innovation-value">{{ $totalTeams }}</span>
                            </div>
                            <div class="innovation-completion">
                                <div class="completion-stat" id="completeAssessmentStat">
                                    <div class="completion-value">{{ $totalCompleteAssessment }}</div>
                                    <div class="completion-label">Penilaian Selesai</div>
                                </div>
                                <div class="completion-stat" id="notCompleteAssessmentStat">
                                    <div class="completion-value">{{ $totalNotCompleteAssessment }}</div>
                                    <div class="completion-label">Penilaian Belum Selesai</div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Complete On Desk Assessment -->
    <div class="modal fade" id="completeTeamsModal" tabindex="-1" aria-labelledby="completeTeamsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header  text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="completeTeamsModalLabel">
                        <i data-feather="zap" class="me-2"></i> <span class="fw-bold">Team yang Sudah Selesai Di Nilai</span>
                    </h5>
                    <button type="button" class="btn-close" style="color: black;" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light">
                    <div class="row">
                        @foreach ($categoriesDataComplete as $categoryName => $teams)
                            <div class="col-md-4 mb-4">
                                <div class="card rounded w-100">
                                    <div class="card-header p-0">
                                        <button 
                                            class="btn btn-primary w-100 text-center rounded-0" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#{{ Illuminate\Support\Str::slug(strtolower($categoryName)) }}" 
                                            aria-expanded="false" 
                                            aria-controls="{{ Illuminate\Support\Str::slug(strtolower($categoryName)) }}">
                                            {{ $categoryName . ' ' . '('.$teams->count().')' }}
                                        </button>
                                    </div>
                                    <div class="collapse card-body p-2" id="{{ Illuminate\Support\Str::slug(strtolower($categoryName)) }}">
                                        @foreach ($teams as $team)
                                            <div>
                                                {{ $team->team_name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

<!-- Not Complete On Desk Assessment -->
    <div class="modal fade" id="notCompleteTeamsModal" tabindex="-1" aria-labelledby="notCompleteTeamsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header text-white bg-primary">
                    <h5 class="modal-title fw-bold d-flex text-white align-items-center" id="notCompleteTeamsModalLabel">
                        <i data-feather="zap" class="me-2"></i>
                        <span class="fw-bold">Tim yang Belum Selesai Dinilai</span>
                    </h5>
                    <button type="button" class="btn-close" style="color: black;" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <div class="modal-body bg-light">
                    <div class="row">
                        @forelse ($categoriesDataNotComplete as $categoryName => $teams)
                            <div class="col-md-4 mb-4">
                                <div class="border-0 rounded">
                                    <p>
                                        <button class="btn btn-primary w-100 text-start" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ Illuminate\Support\Str::slug(strtolower($categoryName)) }}"
                                            aria-expanded="false"
                                            aria-controls="{{ Illuminate\Support\Str::slug(strtolower($categoryName)) }}">
                                            {{ $categoryName }} ({{ $teams->count() }}) {{-- jumlah tim --}}
                                        </button>
                                    </p>
    
                                    <div class="collapse card p-3"
                                        id="{{ Illuminate\Support\Str::slug(strtolower($categoryName)) }}">
                                        @foreach ($teams as $teamName => $judges)
                                            <div class="mb-3">
                                                <strong class="text-dark">{{ $teamName }}</strong>
                                                <ul class="mb-1 ps-3">
                                                    @foreach ($judges as $judge)
                                                        <li>{{ $judge->judge_name }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info">Semua tim telah dinilai oleh seluruh juri.</div>
                            </div>
                        @endforelse
                    </div>
                </div>
    
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#completeAssessmentStat').click(function() {
        // Buka modal
        $('#completeTeamsModal').modal('show');
    });

    $('#notCompleteAssessmentStat').click(function() {
        // Buka modal
        $('#notCompleteTeamsModal').modal('show');
    });
</script>