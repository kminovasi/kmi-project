<div class="card team-card border-0 shadow-lg mt-3">
    <div class="card-header bg-gradient-primary py-3">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="card-title mb-0 fw-bold text-white">Total Tim Terverifikasi Oleh Pengelola Inovasi</h5>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php
                $colors = [
                    [
                        'bg' => '#e7efff',
                        'text' => '#2f4858',
                    ],
                    [
                        'bg' => '#9aa9e0',
                        'text' => '#2f4858',
                    ],
                    [
                        'bg' => '#e7efff',
                        'text' => '#2f4858',
                    ],
                    [
                        'bg' => '#9aa9e0',
                        'text' => '#2f4858',
                    ],
                ];
            @endphp
            @foreach ($teamData as $year => $count)
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm rounded-4 position-relative overflow-hidden" style="background: {{ $colors[$loop->index % count($colors)]['bg'] }};">
                        <div class="card-body py-4 position-relative z-1 text-center text-white">
                            <h6 class="text-uppercase mb-2 opacity-75">Tahun {{ $year }}</h6>
                            <div class="display-6 fw-bold" style="color: {{ $colors[$loop->index % count($colors)]['text'] }};">{{ $count }}</div>
                            <div class="mt-2 small opacity-75"  style="color: {{ $colors[$loop->index % count($colors)]['text'] }};">Tim Terdaftar</div>
                        </div>
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-10"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <div class="card-footer bg-light border-0 text-center">
        <small class="text-muted">
            <i class="bi bi-info-circle me-1"></i>
            Total tim yang diterima dalam 4 tahun terakhir
        </small>
        <br>
        <a href="{{ route('dashboard.showTotalTeamChart') }}" class="btn btn-md mt-3 teams-chart-btn" style="border-radius: 10px;">
            Lihat Chart Total Tim
        </a>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #eb4a3a 0%, #ff6b6b 100%);
    }

    .team-card .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .team-card .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175);
        cursor: pointer;
    }

    .teams-chart-btn {
        width: 100%;
        background-color: #4b5d8e;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .teams-chart-btn:hover {
        background-color: #41527e;
        color: white;
    }
</style>
