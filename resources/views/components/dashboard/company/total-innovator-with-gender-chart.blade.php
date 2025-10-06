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

{{-- 1) LOAD MODULES DULU --}}
<script type="module" src="{{ asset('build/assets/totalInnovatorWithGenderChart-90dc7d1e.js') }}"></script>
<script type="module" src="{{ asset('build/assets/exportTotalInnovatorWithGender-a159c879.js') }}"></script>

{{-- 2) BARU INISIALISASI + PASS growthPerEventData --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const chartDataTotalInnovatorWithGenderChart = @json($chartData ?? []);
    const company_name = @json($company_name ?? '');
    // >>> data pertumbuhan per event (dari method PHP baru)
    window.growthPerEventData = @json($growthPerEventData ?? []);

    window.chartDataTotalInnovatorWithGenderChart = chartDataTotalInnovatorWithGenderChart;
    window.company_name = company_name;

    if (typeof window.renderTotalInnovatorWithGenderChart === 'function') {
        // fungsi ini sekarang akan memanggil renderSummary(chartData, window.growthPerEventData)
        window.renderTotalInnovatorWithGenderChart(chartDataTotalInnovatorWithGenderChart);
    } else {
        // fallback kalau modul belum siap (jarang kejadian, tapi aman)
        window.addEventListener('load', () => {
            if (typeof window.renderTotalInnovatorWithGenderChart === 'function') {
                window.renderTotalInnovatorWithGenderChart(chartDataTotalInnovatorWithGenderChart);
            }
        });
    }
});
</script>
