@push('css')
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

    .financial-benefits {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .financial-benefit-item {
        flex: 1;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .financial-benefit-item:hover {
        background-color: #f1f3f5;
        border-color: #e9ecef;
        transform: translateY(-5px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .financial-benefit-year {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .financial-benefit-total {
        font-size: 1.3rem;
        font-weight: 600;
        color: #343a40;
    }

    @media (max-width: 768px) {
        .financial-benefits {
            flex-direction: column;
        }
    }

    .financial-benefit-item:nth-child(1) {
        background-color: #ffbaba;
        /* Kuning terang */
        color: #856404;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .financial-benefit-item:nth-child(2) {
        background-color: #c3e6cb;
        /* Hijau terang */
        color: #155724;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .financial-benefit-item:nth-child(3) {
        background-color: #bee5eb;
        /* Biru terang */
        color: #0c5460;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .financial-benefit-item:nth-child(4) {
        background-color: #f5c6cb;
        /* Merah terang */
        color: #721c24;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .financial-benefit-item:nth-child(odd) {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .financial-benefit-item:nth-child(even) {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

</style>
@endpush
<div class="card team-card border-0 shadow-lg mt-3">
    <div class="card-header bg-gradient-primary">
        <h5 class="card-title text-white">Total Benefit per Tahun (SIG Grup)</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-muted">Finansial Benefit</h6>
                <div class="financial-benefits">
                    @foreach ($financialBenefits as $benefit)
                    <div class="financial-benefit-item">
                        <span class="financial-benefit-year">
                            {{ $benefit['year'] }}
                        </span>
                        <span class="financial-benefit-total">
                            Rp {{ $benefit['total'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-12">
                <h6 class="text-muted">Potensial Benefit</h6>
                <div class="financial-benefits">
                    @foreach ($potentialBenefits as $benefit)
                    <div class="financial-benefit-item">
                        <span class="financial-benefit-year">
                            {{ $benefit['year'] }}
                        </span>
                        <span class="financial-benefit-total">
                            Rp {{ $benefit['total'] }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row">
            <a href="{{ route('dashboard.showTotalBenefitChart') }}" class="btn link-total-team-chart mt-2">
                Lihat Chart Total Benefit
            </a>
        </div>
    </div>
</div>
