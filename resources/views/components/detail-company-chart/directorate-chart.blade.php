<div class="chart-container">
    <h2 class="chart-title">Distribusi Ide dan Inovasi per Direktorat</h2>
    <div class="chart-wrapper">
        <canvas id="directorateChart"></canvas>
    </div>
    <div class="chart-legend" id="chartLegend"></div>
</div>

@vite(['resources/js/company/companyDashboardChart.js'])

<script>
    window.directorateData = @json($directorateData);
</script>

<style>
    .chart-container {
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin: 20px 0;
    }

    .chart-title {
        font-size: 1.3rem;
        color: #333;
        margin-bottom: 25px;
        text-align: center;
        font-weight: 500;
    }

    .chart-wrapper {
        position: relative;
        height: 400px;
    }

    .chart-legend {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 25px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        margin: 5px 15px;
    }

    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        margin-right: 8px;
    }

    .legend-label {
        font-size: 0.9rem;
        color: #555;
    }
</style>
