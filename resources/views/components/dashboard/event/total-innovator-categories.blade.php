<div class="card p-3 mt-3">
    <h5 class="text-center">Total Inovator per Kategori</h5>
    <canvas id="totalInnovatorChart"></canvas>
    <div class="mt-3 text-center">
        <button class="btn btn-success export-excel-totalInnovatorCategories">Export to Excel</button>
        <button class="btn btn-danger export-pdf-totalInnovatorCategories">Export to PDF</button>
    </div>
</div>

<script type="module">
    import { renderTotalInnovatorChart } from "{{ Vite::asset('resources/js/event/totalInnovatorCategories.js') }}";

    const chartData = @json($chartData);
    const event_name = @json($event_name);
    window.chartData = chartData; // Store chart data globally
    window.event_name = event_name; // Store event name globally
    renderTotalInnovatorChart('totalInnovatorChart', chartData);
</script>
@vite(['resources/js/event/exportTotalInnovatorCategories.js'])
