<div class="card p-3">
    <h2 class="chart-title text-center">Total Finansial Benefit Per
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
    <canvas id="totalFinancialChart"></canvas>
    <div class="mt-3 text-center">
        <button class="btn btn-success export-excel-totalFinancialBenefitByOrganizationChart">Export to Excel</button>
        <button class="btn btn-danger export-pdf-totalFinancialBenefitByOrganizationChart">Export to PDF</button>
    </div>
</div>

@vite(['resources/js/totalFinancialBenefitByOrganizationChart.js'])

<script type="module">
    import {
        initializeTotalFinancialChart
    } from "{{ Vite::asset('resources/js/totalFinancialBenefitByOrganizationChart.js') }}";

    const chartData = @json($chartData); // Kirim data ke JavaScript
    const company_name = @json($company_name);
    initializeTotalFinancialChart(chartData); // Panggil fungsi dari file JS
    window.chartData = chartData; // Store chart data globally
    window.organizationUnitLabel = organizationUnitLabel; // Store organization unit label globally
    window.company_name = company_name; // Store company name globally
</script>

@vite(['resources/js/exportTotalFinancialBenefitByOrganizationChart.js'])
