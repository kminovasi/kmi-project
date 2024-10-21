<div>

        <style>
            /* Styling untuk menyesuaikan ukuran dan tampilan pada perangkat kecil */
            @media (max-width: 600px) {
                .chart-container {
                    min-width: 100%;
                    /* Penyesuaian untuk perangkat kecil */
                    flex-basis: 100%;
                }
            }
            /* Styling untuk menjaga ukuran logo tetap konsisten */
            .logo-image {
                width: 60px;
                height: 60px; /* Menetapkan tinggi maksimum agar gambar tetap simetris */
                object-fit: contain; /* Mengatur gambar agar sesuai di dalam kotak */
                margin-bottom: 10px;
            }
            /* Styling container untuk memastikan chart memiliki ukuran yang konsisten */
            .chart-container {
                flex: 1;
                min-width: 200px;
                text-align: center;
            }
        </style>

    <div>
        <h4>Jumlah Innovator per Tahun per Perusahaan</h4>

        <div style="display: flex; flex-wrap: wrap; gap: 20px;">
            @foreach ($charts as $index => $chart)
                <div class="chart-container">
                    <!-- Menampilkan logo perusahaan -->
                    <img src="{{ $logos[$index] }}" alt="Logo Perusahaan" class="logo-image">
                    <!-- Menampilkan chart -->
                    {!! $chart->container() !!}
                    <script src="{{ $chart->cdn() }}"></script>
                    {{ $chart->script() }}
                </div>
            @endforeach
        </div>
    </div>
</div>
