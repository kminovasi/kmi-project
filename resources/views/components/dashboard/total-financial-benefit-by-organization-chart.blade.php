<div class="card p-3">
    <h2 class="chart-title text-center">Total Financial Benefit Per
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
</div>

@vite(['resources/js/totalFinancialBenefitByOrganizationChart.js'])

<script type="module">
    import {
        initializeTotalFinancialChart
    } from "{{ Vite::asset('resources/js/totalFinancialBenefitByOrganizationChart.js') }}";

    const chartData = @json($chartData); // Kirim data ke JavaScript
    initializeTotalFinancialChart(chartData); // Panggil fungsi dari file JS
</script>
