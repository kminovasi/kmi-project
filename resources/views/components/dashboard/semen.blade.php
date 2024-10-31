<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            <h4 class="mb-0">Jumlah Pendaftar Inovasi per Kategori per Perusahaan</h4>
            <button type="button" class="btn btn-primary btn-sm btn-filter" data-bs-toggle="modal"
                data-bs-target="#yearFilterInnovator">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
        </div>

        <div class="chart-grid">
            @foreach ($charts as $index => $chart)
                <div class="chart-container">
                    <img src="{{ $logos[$index] }}" alt="Logo Perusahaan" class="logo-image">
                    <canvas id="chart-{{ $index }}"></canvas>
                </div>

                <script>
                    (function() {
                        // Plugin kustom untuk menggambar nilai
                        const drawValuePlugin = {
                            id: 'drawValue',
                            afterDatasetsDraw: (chart, args, options) => {
                                const ctx = chart.ctx;
                                chart.data.datasets.forEach((dataset, datasetIndex) => {
                                    const meta = chart.getDatasetMeta(datasetIndex);
                                    if (!meta.hidden) {
                                        meta.data.forEach((element, index) => {
                                            const value = dataset.data[index];
                                            const position = element.tooltipPosition();

                                            ctx.fillStyle = 'black';
                                            ctx.font = '12px Arial';
                                            ctx.textAlign = 'center';
                                            ctx.textBaseline = 'middle';
                                            ctx.fillText(value, position.x, position.y);
                                        });
                                    }
                                });
                            }
                        };

                        // Register plugin
                        Chart.register(drawValuePlugin);

                        let ctx = document.getElementById('chart-{{ $index }}').getContext('2d');
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: {!! json_encode($chart['categories']) !!},
                                datasets: [{
                                    label: '{{ $chart['company'] }}',
                                    data: {!! json_encode($chart['data']) !!},
                                    backgroundColor: {!! json_encode(
                                        array_map(function ($cat) use ($categories) {
                                            return $categories[$cat] ?? '#ffffff';
                                        }, $chart['categories']),
                                    ) !!},
                                    borderWidth: 1,
                                    barThickness: 10
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            color: '#000'
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.1)'
                                        }
                                    },
                                    x: {
                                        display: false,
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.1)'
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: '#000'
                                        }
                                    },
                                    drawValue: true // Enable custom plugin
                                }
                            }
                        });
                    })
                    ();
                </script>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
