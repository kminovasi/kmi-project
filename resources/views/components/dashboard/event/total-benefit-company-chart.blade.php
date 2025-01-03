<div class="container py-4">
    @if ($event->type === 'group' || $event->type === 'internal' || $event->type === 'national' || $event->type === 'international')
        <!-- Card and Chart for Group Event -->
        <div class="card p-3 mb-4">
            <div class="card-header">
                <h5>Total Benefit Group Event</h5>
            </div>
            <div class="card-body">
                <h5 class="card-title">Total Financial Benefit</h5>
                <p class="card-text">
                </p>

                <!-- Logo and Benefits for each company -->
                @foreach ($companies as $company)
                    <div class="company-info mb-2">
                        <img src="{{ $company['logo'] }}" alt="{{ $company['company_name'] }} logo" width="50"
                            height="50">
                        <strong>{{ $company['company_name'] }}</strong>: Rp
                        {{ number_format($company['total_benefit'], 0, ',', '.') }}
                    </div>
                @endforeach

                <!-- Chart Container -->
                <canvas id="totalBenefitChart" width="400" height="400"></canvas>
            </div>
        </div>

        <!-- Push JS to load the chart -->
        @push('js')
            <script type="module">
                import {
                    renderTotalBenefitChart
                } from "{{ Vite::asset('resources/js/event/totalBenefitCompanyChart.js') }}";

                // Data untuk Chart.js
                const chartData = @json($chartData);

                // Render chart
                renderTotalBenefitChart(chartData);
            </script>
        @endpush
    @elseif($event->type === 'AP')
        <!-- Card for AP Event -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Total Financial Benefit AP Event</h5>
            </div>
            <div class="card-body">
                <p class="card-text">
                    <strong>Rp {{ number_format($totalBenefit, 0, ',', '.') }}</strong>
                </p>
            </div>
        </div>
    @else
        <!-- Placeholder for other event types if needed -->
        <div class="alert alert-warning" role="alert">
            Event type tidak dikenali. Tidak ada data yang tersedia.
        </div>
    @endif
</div>
