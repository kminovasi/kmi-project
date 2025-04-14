<style>
    .financial-charts {
        background-color: #f9f9f9;
        padding: 2rem;
        border-radius: 12px;
    }

    .financial-card {
        background-color: white;
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(235, 74, 58, 0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: transform 0.3s ease;
        cursor: pointer;
    }

    .financial-card:hover {
        transform: translateY(-10px);
    }

    .financial-card h3 {
        color: #000;
        font-weight: 600;
        margin-bottom: 1rem;
        text-align: center;
    }

    .financial-chart {
        height: 250px;
        width: 100%;
    }
</style>

<div class="financial-charts container mt-3">
    <div class="container-fluid bg-light mb-3 p-3 rounded shadow-sm">
        <h3 class="text-center text-dark">Perkembangan Benefit Perusahaan Pertahun</h3>
    </div>
    <div class="row">
        @foreach ($financialData as $data)
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="financial-card">
                    <h3>{{ $data['company_name'] }}</h3>
                    <canvas id="chart-{{ $loop->index }}" class="financial-chart"></canvas>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @foreach ($financialData as $data)
            initializeChart(
                'chart-{{ $loop->index }}',
                {!! json_encode(array_keys($data['financials'])) !!},
                {!! json_encode(array_values($data['financials'])) !!}, {
                    primaryColor: '#eb4a3a',
                    hoverEffect: true
                }
            );
        @endforeach
    });
</script>

@vite(['resources/js/financialBenefitChartCompanies.js'])
