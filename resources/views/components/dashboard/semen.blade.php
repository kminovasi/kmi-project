<div>
    <style>
        .dashboard-section {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .logo-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .chart-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .category-item {
            display: flex;
            align-items: center;
            margin-right: 15px;
        }

        .badge-a {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 5px;
            border: 1px solid #ddd;
        }

        .btn-filter {
            margin-bottom: 20px;
        }
    </style>

    <div class="dashboard-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">
                Persebaran Inovasi setiap Perusahaan (SIG Group - Kategori)
            </h4>
            <button
                type="button"
                class="btn btn-primary btn-sm btn-filter"
                data-bs-toggle="modal"
                data-bs-target="#yearFilterInnovator"
            >
                <i class="fas fa-filter me-1"></i> Filter
            </button>
        </div>


        <div class="chart-grid">
            @foreach ($charts as $index => $chart)
                <div class="chart-container">
                    <img src="{{ $logos[$index] }}" alt="Logo Perusahaan" class="logo-image">
                    <canvas id="persebaran-inovasi-perusahaan-chart-{{ $index }}"></canvas>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Kategori</h5>
                @if ($isSuperadmin)
                    <a href="{{ route('detail-company-chart') }}" class="btn btn-outline-primary btn-sm">Detail
                        chart</a>
                @else
                    <a href="{{ route('detail-company-chart-show', ['id' => $companyId]) }}"
                        class="btn btn-outline-primary btn-sm">Detail chart</a>
                @endif
            </div>
            <div class="category-list" id="category-colors">
                @foreach ($categories as $categoryName => $color)
                    <div class="category-item">
                        <div class="badge-a" style="background-color: {{ $color }};"></div>
                        <span>{{ $categoryName }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="yearFilterInnovator" tabindex="-1" aria-labelledby="yearFilterInnovatorLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('dashboard') }}" method="GET">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="yearFilterInnovatorLabel">Filter berdasarkan Tahun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="year" class="form-label">Pilih Tahun</label>
                        <select name="year" id="year" class="form-select">
                            @foreach ($availableYears as $yearOption)
                                <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>
                                    {{ $yearOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script id="charts-data-persebaran-inovasi-setiap-perusahaan" type="application/json">@json($charts)</script>
<script id="categories-data" type="application/json">@json($categories)</script>
@vite(['resources/js/semenChart.js'])
