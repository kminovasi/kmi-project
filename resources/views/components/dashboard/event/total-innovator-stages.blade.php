<div class="card p-3 mt-3">
    <h5 class="text-center">Total Team per Tahap Penilaian</h5>
    <canvas id="totalInnovatorStagesChart"></canvas>
    <div class="mt-3 text-center">
        <button class="btn btn-success export-excel-totalInnovatorStages">Export to Excel</button>
        <button class="btn btn-danger export-pdf-totalInnovatorStages">Export to PDF</button>
    </div>
</div>

<script type="module" src="{{ asset('build/assets/totalInnovatorStages-e429d7f8.js') }}"></script>
<script type="module" src="{{ asset('build/assets/exportTotalInnovatorStages-152e8980.js') }}"></script>

<script type="module">
document.addEventListener("DOMContentLoaded", () => {
    const chartData = @json($chartData) ?? {};
    const canvasId = "totalInnovatorStagesChart";

    if (
        !chartData ||
        !Array.isArray(chartData.labels) ||
        !Array.isArray(chartData.data) ||
        !document.getElementById(canvasId)
    ) return;

    if (typeof window.renderTotalInnovatorStagesChart === "function") {
        window.renderTotalInnovatorStagesChart(canvasId, chartData);
    }
});
</script>
