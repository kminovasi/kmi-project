<div class="container mt-3">
    <div class="card">
        <div class="card-header" style="background-color: #eb4a3a">
            <h5 class="text-white">Jumlah Keterlibatan Inovator</h5>
        </div>
        <div class="card-body">
            <canvas id="total-innovator-chart"></canvas>
        </div>
    </div>

    <!-- Tambahkan chartData sebagai JSON -->
    <script>
        const chartData = @json($chartData);
    </script>
</div>
@vite(['resources/js/totalInnovatorChartInternal.js'])
