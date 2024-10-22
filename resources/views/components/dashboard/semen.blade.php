<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .logo-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .chart-container {
            flex: 1;
            min-width: 200px;
            text-align: center;
            padding: 20px;
            background-color: rgba(240, 240, 240, 0.9);
            /* Latar abu-abu sangat terang agar chart terpisah dari latar belakang */
            border-radius: 10px;
            padding: 10px;
        }

        .category-list {
            display: flex;
            flex-wrap: wrap;
            /* Membuat baris baru jika tidak muat */
            margin-top: 10px;
            /* Jarak antara judul dan daftar kategori */
        }

        .chart-container canvas {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            /* Tambahkan bayangan ringan */
            border-radius: 10px;
            /* Membuat sudut canvas lebih halus */
            background-color: rgba(255, 255, 255, 0.9);
            /* Latar belakang putih semi-transparan */
        }

        .badge-a {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
            vertical-align: middle;
            border: 1px solid #000;
            /* Berikan border hitam untuk menonjolkan badge */
        }

        .category-list span {
            color: #000;
            /* Teks hitam agar terlihat jelas di latar putih */
        }
    </style>

    <div>
        <h4>Jumlah Pendaftar Inovasi per Kategori per Perusahaan </h4>
        <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal"
            data-bs-target="#yearFilterInnovator">
            Filter
        </button>

        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            @foreach ($charts as $index => $chart)
                <div class="chart-container">
                    <img src="{{ $logos[$index] }}" alt="Logo Perusahaan" class="logo-image">
                    <canvas id="chart-{{ $index }}"></canvas>
                </div>

                <script>
                    (function() {
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
                                            return $categories[$cat] ?? '#ffffff'; // Warna default jika kategori tidak ada
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
                                            color: '#000' // Teks hitam
                                        },
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.1)' // Grid abu-abu terang
                                        }
                                    },
                                    x: {
                                        display: false,
                                        grid: {
                                            color: 'rgba(0, 0, 0, 0.1)' // Grid sumbu X abu-abu terang
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        labels: {
                                            color: '#000' // Teks hitam untuk legend
                                        }
                                    }
                                }
                            }

                        });
                    })
                    ();
                </script>
            @endforeach
        </div>
    </div>

    <!-- Div untuk menampilkan warna kategori -->
    <div class="chart-container">
        <h5>Kategori</h5>
        <div class="category-list" id="category-colors">
            @foreach ($categories as $categoryName => $color)
                <div>
                    <div class="badge-a" style="background-color: {{ $color }};"></div>
                    <span class="text-black">{{ $categoryName }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="yearFilterInnovator" tabindex="-1" aria-labelledby="yearFilterInnovatorLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('dashboard') }}" method="GET"> <!-- Pastikan rute sudah disiapkan -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="yearFilterInnovatorLabel">Filter berdasarkan Tahun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <label for="year">Pilih Tahun</label>
                        <select name="year" id="year" class="form-control">
                            @foreach ($availableYears as $yearOption)
                                <option value="{{ $yearOption }}" {{ $year == $yearOption ? 'selected' : '' }}>
                                    {{ $yearOption }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
