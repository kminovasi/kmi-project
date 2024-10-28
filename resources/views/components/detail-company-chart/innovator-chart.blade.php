@vite(['resources/js/innovatorChart.js'])
<div>
    <canvas id="{{ $chartId }}"></canvas>
</div>
<script>
    window.chartData = window.chartData || {};
    window.chartData['{{ $chartId }}'] = @json($chartData);
</script>
