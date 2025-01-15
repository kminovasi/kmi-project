<div class="card p-3 mb-4">
    <h5 class="text-center">Total Innovator per Tahun</h5>
    <div class="row">
        <canvas id="totalInnovatorWithGenderChart"></canvas>
    </div>
    <div class="row">
        <div class="mt-3 text-center">
            <button class="btn btn-success export-excel-totalInnovatorWithGender">Export to Excel</button>
            <button class="btn btn-danger export-pdf-totalInnovatorWithGender">Export to PDF</button>
        </div>
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
    const company_name = @json($company_name);
    window.chartDataTotalInnovatorWithGenderChart = chartDataTotalInnovatorWithGenderChart; // Store chart data globally
    window.company_name = company_name; // Store organization unit label globally
    renderTotalInnovatorWithGenderChart(chartDataTotalInnovatorWithGenderChart);
</script>

@vite(['resources/js/company/exportTotalInnovatorWithGender.js'])
