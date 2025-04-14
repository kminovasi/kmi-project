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

    .benefit-chart-btn {
        display: inline-block;
        padding: 10px 15px;
        background-color: #4b5d8e;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .benefit-chart-btn:hover {
        background-color: #41527e;
        color: white;
    }

    .financial-benefits {
        display: flex;
        gap: 15px;
        margin-top: .7rem;
    }

    .financial-benefit-item {
        flex: 1;
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: .7rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .financial-benefit-item:hover {
        background-color: #f1f3f5;
        border-color: #e9ecef;
        transform: translateY(-5px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .financial-benefit-year {
        font-size: 0.9rem;
        color: #cacaca;
        margin-bottom: 8px;
        display: block;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .financial-benefit-total {
        font-size: 1.1rem;
        color: #fcfcfc;
        font-weight: 600;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .financial-benefits {
            flex-direction: column;
        }
    }

    .financial-benefit-item:nth-child(1) {
        background-color: #38507a;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .financial-benefit-item:nth-child(2) {
        background-color: #2f4858;
        color: #9aa9e0;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .financial-benefit-item:nth-child(3) {
        background-color: #38507a;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 10px;
        font-weight: bold;
    }

    .financial-benefit-item:nth-child(4) {
        background-color: #2f4858;  
        color: #9aa9e0;
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
        <h5 class="card-title text-white">Total Benefit Finansial (SIG Group)</h5>
    </div>
    <div class="card-body" id="financialBenefitsData"
         data-benefits='@json($financialBenefits)'
         data-potential-benefits='@json($potentialBenefits)'>
        <div class="row">
            <div class="col-md-12">
                <h6 class="text-muted">Benefit Finansial (Riil)</h6>
                <div id="financialBenefits" class="financial-benefits"></div>
            </div>
            <div class="col-md-12 mt-3">
                <h6 class="text-muted">Benefit Finansial (Potensial)</h6>
                <div id="potentialBenefits" class="financial-benefits"></div>
            </div>
        </div>
        <div class="row">
            <a href="{{ route('dashboard.showTotalBenefitChart') }}" class="btn benefit-chart-btn mt-2">
                Lihat Chart Total Benefit
            </a>
        </div>
    </div>
</div>

