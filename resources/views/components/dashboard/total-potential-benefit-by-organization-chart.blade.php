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
    <canvas id="totalPotentialChart" style="width: 100%;"></canvas>
    <div class="mt-3 text-end">
        <button class="btn btn-sm btn-success export-excel-totalPotentialBenefitByOrganizationChart">Export to Excel</button>
        <button class="btn btn-sm btn-danger export-pdf-totalPotentialBenefitByOrganizationChart">Export to PDF</button>
    </div>
</div>

{{-- <!--@vite(['resources/js/totalPotentialBenefitByOrganizationChart.js'])--> --}}

<script type="module">
    document.addEventListener("DOMContentLoaded", function () {
        const chartData = @json($chartData); // Kirim data ke JavaScript
        const company_name = @json($company_name);
        
        // Simpan ke global
        window.chartData = chartData;
        window.company_name = company_name;

        // Debug log
        // console.log("[DEBUG][Potential] chartData:", chartData);
        // console.log("[DEBUG][Potential] company_name:", company_name);
        // console.log("[DEBUG][Potential] typeof window.initializeTotalPotentialChart =", typeof window.initializeTotalPotentialChart);

        // Render chart jika fungsi tersedia
        if (typeof window.initializeTotalPotentialChart === 'function') {
            // console.log("[DEBUG][Potential] Memanggil initializeTotalPotentialChart...");
            window.initializeTotalPotentialChart(chartData);
        } else {
            // console.warn("[DEBUG][Potential] initializeTotalPotentialChart belum tersedia! Pastikan file JS sudah termuat.");
        }
    });
</script>

<script type="module" src="{{ asset('build/assets/totalPotentialBenefitByOrganizationChart-4c545d78.js') }}"></script>
<script type="module" src="{{ asset('build/assets/exportTotalPotentialBenefitByOrganizationChart-f7299526.js') }}"></script>
