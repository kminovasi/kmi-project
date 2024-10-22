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
        }

        .badge-a {
            display: inline-block;
            width: 20px; /* Lebar badge */
            height: 20px; /* Tinggi badge */
            border-radius: 50%; /* Bentuk bulat */
            margin-right: 10px; /* Jarak antara badge dan nama kategori */
            vertical-align: middle; /* Vertikal align dengan teks */
        }

        .category-list {
            display: flex;
            flex-wrap: wrap; /* Membuat baris baru jika tidak muat */
            margin-top: 10px; /* Jarak antara judul dan daftar kategori */
        }
    </style>

    <div>
        <h4>Jumlah Pendaftar Inovasi per Kategori per Perusahaan (Tahun Terbaru)</h4>

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
                                    backgroundColor: {!! json_encode(array_map(function($cat) use ($categories) { return $categories[$cat]; }, $chart['categories'])) !!},
                                    borderWidth: 1,
                                    barThickness: 10
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    },
                                    x: {
                                        display: false // Menyembunyikan label sumbu x
                                    },
                                },
                                plugins: {
                                    legend: {
                                        display: false // Menyembunyikan legend di semua chart kecuali yang terakhir
                                    }
                                }
                            }
                        });
                    })();
                </script>
            @endforeach
        </div>
    </div>

    <!-- Div untuk menampilkan warna kategori -->
    <div class="chart-container">
        <h5>Warna Kategori</h5>
        <div class="category-list" id="category-colors">
            @foreach ($categories as $categoryName => $color)
                <div>
                    <div class="badge-a" style="background-color: {{ $color }};"></div>
                    {{ $categoryName }}
                </div>
            @endforeach
        </div>
    </div>
</div>
