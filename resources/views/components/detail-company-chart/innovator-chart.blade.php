@vite(['resources/js/innovatorChart.js'])
<div>
    <canvas id="{{ $chartId }}" style="height: 300px;"></canvas>
</div>
<script>
    window.chartData = window.chartData || {};
    window.chartData['{{ $chartId }}'] = @json($chartData);
</script>
