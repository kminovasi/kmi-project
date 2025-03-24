<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Filter Berdasarkan Unit Organisasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm" action="{{ route('detail-company-chart-show', ['companyId' => $companyId]) }}" method="GET">
                    <div class="mb-3">
                        <label for="organizationLevel" class="form-label">Pilih Tingkat Organisasi</label>
                        <select class="form-select" id="organizationLevel" name="organization-unit">
                            <option selected disabled>Pilih Tingkat Organisasi</option>
                            <option value="directorate_name">Direktorat</option>
                            <option value="group_function_name">Grup</option>
                            <option value="department_name">Departemen</option>
                            <option value="unit_name">Unit</option>
                            <option value="section_name">Seksi</option>
                            <option value="sub_section_of">Sub Seksi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="year">Pilih Tahun:</label>
                        <select name="year" id="year" class="form-control">
                            <option value="">Pilih Tahun</option>
                            @foreach ($availableYears as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                <button type="submit" class="btn btn-primary" form="filterForm">Terapkan Filter</button>
            </div>
        </div>
    </div>
</div>
