<div class="card p-3 mb-4">
    <h5 class="text-center">Total Innovator per Tahun</h5>
    <div class="row">
        <canvas id="totalInnovatorWithGenderChart" style="width: 100%; height: 20rem;"></canvas>
    </div>
    <div class="row">
        <div id="chartSummary"></div>
    </div>
    <div class="row">
        <div class="mt-3 text-end">
            <button class="btn btn-sm btn-success export-excel-totalInnovatorWithGender">Export to Excel</button>
            <button class="btn btn-sm btn-danger export-pdf-totalInnovatorWithGender">Export to PDF</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chartDataTotalInnovatorWithGenderChart = @json($chartData);
        const company_name = @json($company_name);
        
        window.chartDataTotalInnovatorWithGenderChart = chartDataTotalInnovatorWithGenderChart;
        window.company_name = company_name;

        // Kalau kamu ingin langsung render dari sini (optional):
        if (typeof window.renderTotalInnovatorWithGenderChart === 'function') {
            window.renderTotalInnovatorWithGenderChart(chartDataTotalInnovatorWithGenderChart);
        }
    });
</script>

<script type="module" src="{{ asset('build/assets/totalInnovatorWithGenderChart-789eb346.js') }}"></script>
<script type="module" src="{{ asset('build/assets/exportTotalInnovatorWithGender-a159c879.js') }}"></script>