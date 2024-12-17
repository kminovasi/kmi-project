<div class="card p-3">
    <h2 class="chart-title text-center">Total Innovator per Organisasi</h2>
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#filterModal">
        Filter by Organization
    </button>
    <canvas id="totalInnovatorEventChart"></canvas>
</div>

<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter by Organization Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" method="GET" action="{{ route('dashboard-event.statistics', ['id' => $eventId]) }}">
                    <div class="mb-3">
                        <label for="organizationLevel" class="form-label">Pilih Tingkat Organisasi</label>
                        <select class="form-select" id="organizationLevel" name="organization-unit">
                            <option disabled selected>Select Organization Level</option>
                            <option value="directorate_name">Direktorat</option>
                            <option value="group_function_name">Group</option>
                            <option value="department_name">Departemen</option>
                            <option value="unit_name">Unit</option>
                            <option value="section_name">Seksi</option>
                            <option value="sub_section_of">Sub Seksi</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Apply Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@vite(['resources/js/totalInnovatorEventChart.js'])

<script type="module">
    import { initializeTotalInnovatorEventChart } from "{{ Vite::asset('resources/js/event/totalInnovatorEventChart.js') }}";

    const chartData = @json($chartData);
    let chartInstance = initializeTotalInnovatorEventChart(chartData);
</script>
