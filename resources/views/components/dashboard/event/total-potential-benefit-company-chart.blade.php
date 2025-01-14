<div class="container py-4">
    @if (
        $event->type === 'group' ||
        $event->type === 'internal' ||
        $event->type === 'national' ||
        $event->type === 'international')
        <!-- Card for Total Benefit Group Event -->
        <div class="card mb-4 p-5 shadow-lg rounded-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <i class="fas fa-coins text-success" style="font-size: 2.5rem;"></i>
                <span class="badge bg-success text-white rounded-pill">Benefit</span>
            </div>
            <h5 class="fw-bold mt-3 fs-2">
                <strong class="text-success">
                    Rp {{ number_format($companies->sum('total_benefit'), 0, ',', '.') }}
                </strong>
            </h5>
            <p class="text-muted">Akumulasi Total Potensial Benefit</p>
        </div>
    @elseif($event->type === 'AP')
        <!-- Card for AP Event -->
        <div class="card mb-4 p-5 shadow-lg rounded-3 border-0">
            <div class="d-flex justify-content-between align-items-center">
                <i class="fas fa-hand-holding-usd text-success" style="font-size: 2.5rem;"></i>
                <span class="badge bg-success text-white rounded-pill">Benefit</span>
            </div>
            <h5 class="fw-bold mt-3 fs-2">
                <strong class="text-success">
                    Rp {{ number_format($companies->sum('total_benefit'), 0, ',', '.') }}
                </strong>
            </h5>
            <p class="text-muted">Akumulasi Total Potensial Benefit</p>
        </div>
    @else
        <!-- Placeholder for Other Event Types -->
        <div class="alert alert-warning text-center fs-4" role="alert">
            Event type tidak dikenali. Tidak ada data yang tersedia.
        </div>
    @endif
</div>

<script>
    // Feather icons initialization
    feather.replace();
</script>
