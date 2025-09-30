<div class="container mt-3">
    <div class="card">
        <div class="card-header" style="background-color: #eb4a3a">
            <h5 class="text-white">Persebaran Inovasi - Perusahaan Grup Non Semen</h5>
        </div>
        <div class="card-body" style="height: 25rem;"> <!-- Pastikan ada tinggi untuk container -->
            <canvas id="non-cement-innovation-chart" style="width: 100%; height: 100%"></canvas>
        </div>
    </div>
</div>


<script>
    window.nonCementInnovationChartData = @json($chartData);
</script>

<script src="{{ asset('build/assets/nonCementInnovationsChart-fbac7df0.js') }}" type="module"></script>