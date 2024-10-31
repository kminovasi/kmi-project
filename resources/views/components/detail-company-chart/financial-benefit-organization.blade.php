<div class="chart-container">
    <h2 class="chart-title">Financial Benefit per
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
    <div class="chart-wrapper">
        <canvas id="financialBenefitOrganization"></canvas>
    </div>
    <div class="chart-legend" id="chartLegend"></div>
</div>

<script>
    window.financialBenefitsData = @json($financialBenefitsByDirectorate);
</script>
