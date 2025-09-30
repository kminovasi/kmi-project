<div class="card p-3">
    <h5 class="chart-title text-center">Total Inovator per Organisasi {{$companyName}} </h5>
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#filterModal">
        <i class="fas fa-filter me-2"></i> Filter Berdasarkan Organisasi
    </button>

    <canvas id="{{ $canvasId }}"></canvas>
    <div class="mt-3 text-center">
        <button class="btn btn-success export-excel-totalInnovatorEventChart">Export to Excel</button>
        <button class="btn btn-danger export-pdf-totalInnovatorEventChart">Export to PDF</button>
    </div>
</div>

<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Berdasarkan Unit Organisasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET" action="{{ route('dashboard-event.statistics', ['id' => $eventId]) }}">
                    <div class="mb-3">
                        <label for="organizationLevel" class="form-label">Pilih Tingkat Organisasi</label>
                        <select class="form-select" id="organizationLevel" name="organization-unit">
                            <option disabled selected>Pilih Tingkat Organisasi</option>
                            <option value="directorate_name">Direktorat</option>
                            <option value="group_function_name">Grup</option>
                            <option value="department_name">Departemen</option>
                            <option value="unit_name">Unit</option>
                            <option value="section_name">Seksi</option>
                            <option value="sub_section_of">Sub Seksi</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="module">
    document.addEventListener("DOMContentLoaded", function () {
        const chartData = @json($chartData);
        const organizationUnit = @json($organizationUnit);
        const event_name = @json($event_name);
        const canvasId = @json($canvasId);

        window.chartDataTotalInnovatorOrganization = chartData;
        window.organizationUnit = organizationUnit;
        window.event_name = event_name;

        if (typeof window.initializeTotalInnovatorEventChart === 'function') {
            window.initializeTotalInnovatorEventChart(chartData, canvasId, organizationUnit);
        }
    });
</script>

<script type="module" src="{{ asset('build/assets/totalInnovatorEventChart-36b7b985.js') }}"></script>
<script type="module" src="{{ asset('build/assets/exportTotalInnovatorEventChart-c99f386d.js') }}"></script>

