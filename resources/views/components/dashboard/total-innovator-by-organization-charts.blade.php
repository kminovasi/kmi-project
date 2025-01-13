<div class="card p-3">
    <h2 class="chart-title text-center">Total Innovator per
        @php
            $labels = [
                'unit_name' => 'Unit',
                'directorate_name' => 'Direktorat',
                'group_function_name' => 'Group',
                'department_name' => 'Departemen',
                'section_name' => 'Seksi',
                'sub_section_of' => 'Sub Seksi',
            ];
        @endphp

        {{ $labels[$organizationUnit] ?? 'Unit Organisasi' }}
    </h2>
    <canvas id="totalInnovatorChart"></canvas>
    <div class="mt-3 text-center">
        <button class="btn btn-success export-excel-exportTotalInnovatorByOrganization">Export to Excel</button>
        <button class="btn btn-danger export-pdf-exportTotalInnovatorByOrganization">Export to PDF</button>
    </div>
</div>

@vite(['resources/js/totalInnovatorByOrganizationChart.js'])

<script type="module">
    import {
        initializeTotalInnovatorChart
    } from "{{ Vite::asset('resources/js/totalInnovatorByOrganizationChart.js') }}";

    const chartData = @json($chartData); // Kirim data ke JavaScript
    const organizationUnitLabel = @json($labels[$organizationUnit] ?? 'Unit Organisasi');
    window.chartData = chartData; // Store chart data globally
    window.organizationUnitLabel = organizationUnitLabel; // Store organization unit label globally
    initializeTotalInnovatorChart(chartData); // Panggil fungsi dari file JS
</script>
@vite(['resources/js/exportTotalInnovatorByOrganization.js'])
