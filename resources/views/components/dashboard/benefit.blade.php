<style>
    canvas {
        width: 100% !important;
        /* Sesuaikan ukuran canvas dengan kontainer */
        height: auto !important;
        /* Sesuaikan proporsinya */
    }

    #benefitChart {
        background-color: #fff;
        /* Ganti dengan warna latar belakang yang Anda inginkan */
        border-radius: 8px;
        /* Contoh: menambahkan sudut bulat pada canvas */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Contoh: menambahkan bayangan untuk menonjolkan chart */
    }
</style>
<div>
    <canvas id="benefitChart"></canvas>
</div>

<!-- Siapkan data dalam elemen data-attributes -->
<div id="chartData" data-labels='@json($charts['labels'] ?? [])' data-data='@json($charts['data'] ?? [])'
    data-logos='@json($charts['logos'] ?? [])'></div>
@vite(['resources/js/app.js'])
