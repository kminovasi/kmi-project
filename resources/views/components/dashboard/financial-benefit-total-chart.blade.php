<div class="mt-3 card p-3">
    <h3>Total Finansial Benefit per tahun</h3>
    <p>{{ $title }}</p>
    <canvas id="financialBenefitChart"></canvas>
</div>

<script>
    const initFinancialBenefitChart = @json($chartData);
</script>

<script src="{{ asset('/build/assets/totalBenefit-3caf2e64.js') }}" type="module"></script>
