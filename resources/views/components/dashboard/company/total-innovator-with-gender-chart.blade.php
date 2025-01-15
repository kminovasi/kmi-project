<div class="card p-3 mb-4">
    <h5 class="text-center">Total Innovator per Tahun</h5>
    <div class="row">
        <canvas id="totalInnovatorWithGenderChart"></canvas>
    </div>
    <div class="row">
        <div id="chartSummary"></div>

    </div>
</div>


<script type="module">
    import {
        renderTotalInnovatorWithGenderChart
    } from "{{ Vite::asset('resources/js/company/totalInnovatorWithGenderChart.js') }}";

    const chartDataTotalInnovatorWithGenderChart = @json($chartData);
    renderTotalInnovatorWithGenderChart(chartDataTotalInnovatorWithGenderChart);
</script>
