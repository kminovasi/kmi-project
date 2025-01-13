<div class="mt-3 card p-3">
    <h3>Total Potensial Benefit per tahun</h3>
    <p>{{ $title }}</p>
    <canvas id="potentialBenefitChart"></canvas>
</div>

<script type="module">
    import {
        initPotentialBenefitChart
    } from "{{ Vite::asset('resources/js/totalBenefit.js') }}";

    document.addEventListener('DOMContentLoaded', function() {
        const chartData = @json($chartData);
        initPotentialBenefitChart(chartData);
    });
</script>
