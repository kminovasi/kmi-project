<div class="chart-container">
    <h2 class="chart-title">Innovator per Direktorat</h2>
    <div class="chart-wrapper">
        <canvas id="innovatorDirectorateChart"></canvas>
    </div>
    <div class="chart-legend" id="chartLegend"></div>
</div>


<script>
    window.innovatorDirectorateData = @json($innovatorsByDirectorate);
</script>

@vite(['resources/js/company/companyDashboardChart.js'])
