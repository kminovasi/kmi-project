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
    <canvas id="totalFinancialChart" style="width: 100%;"></canvas>
    <div class="mt-3 text-end">
        <button class="btn btn-sm btn-success export-excel-totalFinancialBenefitByOrganizationChart">Export to Excel</button>
        <button class="btn btn-sm btn-danger export-pdf-totalFinancialBenefitByOrganizationChart">Export to PDF</button>
    </div>
</div>

{{-- @vite(['resources/js/totalFinancialBenefitByOrganizationChart.js']) --}}

<script type="module">
    document.addEventListener("DOMContentLoaded", function () {
        const chartData = @json($chartData); // Kirim data ke JavaScript
        const company_name = @json($company_name);
        
        window.chartData = chartData; // Store chart data globally
        window.company_name = company_name; // Store company name globally

        // Kalau kamu ingin langsung render dari sini (optional):
        if (typeof window.initializeTotalFinancialChart === 'function') {
            window.initializeTotalFinancialChart(chartData);
        }
    });
</script>

<script type="module" src="{{ asset('build/assets/totalFinancialBenefitByOrganizationChart-c2fb24e1.js') }}"></script>
<script type="module" src="{{ asset('build/assets/exportTotalFinancialBenefitByOrganizationChart-78123bca.js') }}"></script>
