<div class="card team-card border-0 shadow-lg mt-3">
    <div class="card-header bg-gradient-primary py-3">
        <div class="d-flex align-items-center">
            <h5 class="card-title mb-0 fw-bold text-white">Total Tim Terverifikasi Oleh Pengelola Inovasi </h5>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php
                $colors = [
                    [
                        'bg' => 'bg-custom-red',
                        'text' => 'text-white',
                        'gradient' => 'linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%)',
                    ],
                    [
                        'bg' => 'bg-custom-green',
                        'text' => 'text-white',
                        'gradient' => 'linear-gradient(135deg, #2ecc71 0%, #27ae60 100%)',
                    ],
                    [
                        'bg' => 'bg-custom-blue',
                        'text' => 'text-white',
                        'gradient' => 'linear-gradient(135deg, #3498db 0%, #2980b9 100%)',
                    ],
                    [
                        'bg' => 'bg-custom-orange',
                        'text' => 'text-white',
                        'gradient' => 'linear-gradient(135deg, #f39c12 0%, #d35400 100%)',
                    ],
                ];
            @endphp
            @foreach ($teamData as $year => $count)
                <div class="col-6 col-md-3">
                    <div
                        class="card {{ $colors[$loop->index % count($colors)]['bg'] }} {{ $colors[$loop->index % count($colors)]['text'] }} text-center rounded-4 position-relative overflow-hidden">
                        <div class="card-body py-4 position-relative z-1">
                            <h6 class="text-uppercase mb-2 opacity-75">Tahun {{ $year }}</h6>
                            <div class="display-6 fw-bold">{{ $count }}</div>
                            <div class="mt-2 small opacity-75">Tim Terdaftar</div>
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
        <a href="{{ route('dashboard.showTotalTeamChart') }}" class="btn link-total-team-chart mt-2">Lihat Chart Total
            Tim</a>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #eb4a3a 0%, #ff6b6b 100%);
        /* Gradient dengan warna primer */
    }

    .team-card .bg-custom-red {
        background: linear-gradient(135deg, #ff6b6b 0%, #ff4757 100%);
        color: white;
    }

    .team-card .bg-custom-green {
        background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        color: white;
    }

    .team-card .bg-custom-blue {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
    }

    .team-card .bg-custom-orange {
        background: linear-gradient(135deg, #f39c12 0%, #d35400 100%);
        color: white;
    }

    .team-card .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .team-card .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175);
    }

    .link-total-team-chart {
        display: inline-block;
        padding: 10px 15px;
        background-color: #eb4a3a;
        /* Warna primer Anda */
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .link-total-team-chart:hover {
        background-color: #c0392b;
        /* Warna lebih gelap saat hover */
    }
</style>
