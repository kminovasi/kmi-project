<div class="card p-3">
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
    <canvas id="totalTeamChart"></canvas>
</div>
@vite(['resources/js/totalTeamByOrganization.js']);

<script type="module">
    import {
        initializeTotalTeamChart
    } from "{{ Vite::asset('resources/js/totalTeamByOrganizationChart.js') }}";
    const chartData = @json($chartData); // Kirim data ke JavaScript
    initializeTotalTeamChart(chartData); // Panggil fungsi dari file JS
</script>
