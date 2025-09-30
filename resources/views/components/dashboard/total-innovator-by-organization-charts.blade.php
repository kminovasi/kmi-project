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
    <canvas id="totalInnovatorChart" style="width: 100%; max-height: 20rem;"></canvas>
    <div class="mt-3 text-end">
        <button class="btn btn-sm btn-success export-excel-exportTotalInnovatorByOrganization">Export to Excel</button>
        <button class="btn btn-sm btn-danger export-pdf-exportTotalInnovatorByOrganization">Export to PDF</button>
    </div>
</div>

<script type="module">
    document.addEventListener("DOMContentLoaded", function () {
        const chartData = @json($chartData); // Kirim data ke JavaScript
        const company_name = @json($company_name);
        
        window.chartData = chartData; // Store chart data globally
        window.company_name = company_name; // Store company name globally

        // Kalau kamu ingin langsung render dari sini (optional):
        if (typeof window.initializeTotalInnovatorChart === 'function') {
            window.initializeTotalInnovatorChart(chartData);
        }
    });
</script>

<script type="module" src="{{ asset('build/assets/totalInnovatorByOrganizationChart-bb4e58f2.js') }}"></script>
<script type="module" src="{{ asset('build/assets/exportTotalInnovatorByOrganization-eef86002.js') }}"></script>
