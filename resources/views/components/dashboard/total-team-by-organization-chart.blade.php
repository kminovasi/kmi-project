<div class="card p-2">
    <h2 class="chart-title text-center">Distribusi Ide
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
    <canvas id="totalTeamChart"  style="width: 100%; height: 20rem;"></canvas>
    <div class="mt-3 text-end">
        <button class="btn btn-sm btn-success export-excel">Export to Excel</button>
        <button class="btn btn-sm btn-danger export-pdf">Export to PDF</button>
    </div>
</div>
@vite(['resources/js/totalTeamByOrganization.js']);

<script type="module">
    import {
        initializeTotalTeamChart
    } from "{{ Vite::asset('resources/js/totalTeamByOrganizationChart.js') }}";
    const chartData = @json($chartData);
    const organizationUnitLabel = @json($labels[$organizationUnit] ?? 'Unit Organisasi');
    const company_name = @json($company_name);
    window.chartData = chartData; // Store chart data globally
    window.organizationUnitLabel = organizationUnitLabel; // Store organization unit label globally
    window.company_name = company_name; // Store organization unit label globally
    initializeTotalTeamChart(chartData); // Panggil fungsi dari file JS
</script>
@vite(['resources/js/exportTotalTeamByOrganization.js'])
