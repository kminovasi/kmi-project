<div class="card p-3">
    <h2 class="chart-title text-center">Total Potensial Benefit Per
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
    <canvas id="totalPotentialChart"></canvas>
    <div class="mt-3 text-center">
        <button class="btn btn-success export-excel-totalPotentialBenefitByOrganizationChart">Export to Excel</button>
        <button class="btn btn-danger export-pdf-totalPotentialBenefitByOrganizationChart">Export to PDF</button>
    </div>
</div>

@vite(['resources/js/totalPotentialBenefitByOrganizationChart.js'])

<script type="module">
    import {
        initializeTotalPotentialChart
    } from "{{ Vite::asset('resources/js/totalPotentialBenefitByOrganizationChart.js') }}";

    const chartData = @json($chartData); // Kirim data ke JavaScript
    const organizationUnitLabel = @json($labels[$organizationUnit] ?? 'Unit Organisasi');
    const company_name = @json($company_name);
    window.chartData = chartData; // Store chart data globally
    window.organizationUnitLabel = organizationUnitLabel; // Store organization unit label globally
    window.company_name = company_name; // Store company name globally
    initializeTotalPotentialChart(chartData); // Panggil fungsi dari file JS
</script>
@vite(['resources/js/exportTotalPotentialBenefitByOrganizationChart.js'])
