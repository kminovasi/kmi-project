<div class="card p-3">
    <div class="card-body">
        <h5 class="card-title">Total Team per Perusahaan</h5>
        <canvas id="chartCanvasTotalTeamCompany"></canvas>
    </div>
</div>
<script type="module">
    import { renderTotalTeamCompanyChart } from "{{ Vite::asset('resources/js/event/totalTeamCompanyChart.js') }}";
    const chartDataTotalTeamCompany = @json($chartData);
    renderTotalTeamCompanyChart(chartDataTotalTeamCompany);
</script>
