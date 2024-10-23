<style>
    canvas {
    width: 100% !important;  /* Sesuaikan ukuran canvas dengan kontainer */
    height: auto !important; /* Sesuaikan proporsinya */
}
</style>
<div>
    <canvas id="benefitChart"></canvas>
</div>

<!-- Siapkan data dalam elemen data-attributes -->
<div id="chartData" data-labels='@json($charts["labels"] ?? [])' data-data='@json($charts["data"] ?? [])' data-logos='@json($charts["logos"] ?? [])'></div>
@vite(['resources/js/app.js'])

