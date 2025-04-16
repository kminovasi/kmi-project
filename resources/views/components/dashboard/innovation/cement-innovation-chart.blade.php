<div class="container mt-3">
    <div class="card">
        <div class="card-header" style="background-color: #eb4a3a">
            <h5 class="text-white">Persebaran Ide Inovasi - Perusahaan Grup Semen</h5>
        </div>
        <div class="card-body" style="height: 25rem;"> <!-- Pastikan ada tinggi untuk container -->
            <canvas id="cement-innovation-chart" style="width: 100%; height: 100%"></canvas>
        </div>
    </div>
</div>


<script>
    window.cementInnovationChartData = @json($chartData);
</script>

@vite(['resources/js/cementInnovationsChart.js'])