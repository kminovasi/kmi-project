<div class="card mt-3">
    <div class="card-header" style="background-color: #eb4a3a">
        <h5 class="text-white">Total Potential Benefit per Perusahaan </h5>
    </div>
    <div class="card-body">
        <canvas id="total-potential-benefit-chart"></canvas>
    </div>
</div>

@vite(['resources/js/totalBenefitChart.js'])

<script>
    const chartDataTotalPotentialBenefit = @json($chartDataTotalPotentialBenefit);
</script>
