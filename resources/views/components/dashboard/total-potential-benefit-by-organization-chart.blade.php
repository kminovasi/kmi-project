<div class="card p-3">
    <h2 class="chart-title text-center">Total Potential Benefit Per
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
</div>

@vite(['resources/js/totalPotentialBenefitByOrganizationChart.js'])

<script type="module">
    import {
        initializeTotalPotentialChart
    } from "{{ Vite::asset('resources/js/totalPotentialBenefitByOrganizationChart.js') }}";

    const chartData = @json($chartData); // Kirim data ke JavaScript
    initializeTotalPotentialChart(chartData); // Panggil fungsi dari file JS
</script>
