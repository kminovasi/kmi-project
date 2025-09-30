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
{{-- <!--@vite(['resources/js/totalTeamByOrganization.js']);--> --}}

<script type="module">
  document.addEventListener("DOMContentLoaded", () => {
    const chartData = @json($chartData);
    const company_name = @json($company_name);

    // Simpan global (kalau perlu diakses file lain)
    window.chartData = chartData;
    window.company_name = company_name;

    // Debug
    // console.log("[totalTeam] chartData:", chartData);
    // console.log("[totalTeam] company_name:", company_name);
    // console.log("[totalTeam] typeof window.initializeTotalTeamChart =", typeof window.initializeTotalTeamChart);

    // Render chart jika fungsi tersedia
    if (typeof window.initializeTotalTeamChart === "function") {
    //   console.log("[totalTeam] Memanggil initializeTotalTeamChart...");
      window.initializeTotalTeamChart(chartData);
    } else {
    //   console.warn("[totalTeam] initializeTotalTeamChart belum tersedia! Cek bundel JS.");
    }
  });
</script>

<script type="module" src="{{ asset('build/assets/totalTeamByOrganizationChart-fdef3c2c.js') }}"></script>
<script type="module" src="{{ asset('build/assets/exportTotalTeamByOrganization-2b7c41f1.js') }}"></script>
