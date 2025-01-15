<div class="card p-3 mt-3">
    <h5 class="text-center">Total Team per Tahap</h5>
    <canvas id="totalInnovatorStagesChart"></canvas>
    <div class="mt-3 text-center">
        <button class="btn btn-success export-excel-totalInnovatorStages">Export to Excel</button>
        <button class="btn btn-danger export-pdf-totalInnovatorStages">Export to PDF</button>
    </div>
</div>

<script type="module">
    import { renderTotalInnovatorStagesChart } from "{{ Vite::asset('resources/js/event/totalInnovatorStages.js') }}";

    const chartDataExportTotalInnovatorStages = @json($chartData);
    const event_name = @json($event_name);
    window.chartDataExportTotalInnovatorStages = chartDataExportTotalInnovatorStages;
    window.event_name = event_name;
    renderTotalInnovatorStagesChart('totalInnovatorStagesChart', chartDataExportTotalInnovatorStages);
</script>

@vite(['resources/js/event/exportTotalInnovatorStages.js'])
