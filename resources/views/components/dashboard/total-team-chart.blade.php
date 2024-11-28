<div class="card border-0 shadow-lg mt-3">
    <div class="card-header bg-gradient-primary text-white py-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-people-fill fs-3 me-3"></i>
            <h5 class="card-title mb-0 fw-bold text-white">Total Tim Terdaftar (Diterima)</h5>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php
                $colors = [
                    'bg-primary text-white',
                    'bg-success text-white',
                    'bg-info text-white',
                    'bg-warning text-dark',
                ];
            @endphp
            @foreach ($teamData as $year => $count)
                <div class="col-6 col-md-3">
                    <div
                        class="card {{ $colors[$loop->index % count($colors)] }} text-center rounded-4 position-relative overflow-hidden">
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
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    }

    .card-body .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card-body .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175);
    }
</style>
