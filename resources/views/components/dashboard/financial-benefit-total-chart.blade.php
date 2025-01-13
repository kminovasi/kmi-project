<div class="mt-3 card p-3">
    <h3>Total Finansial Benefit per tahun</h3>
    <p>{{ $title }}</p>
    <canvas id="financialBenefitChart"></canvas>
</div>

<script type="module">
    import {
        initFinancialBenefitChart
    } from "{{ Vite::asset('resources/js/totalBenefit.js') }}";

    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);
        initFinancialBenefitChart(chartData);
    });
</script>
