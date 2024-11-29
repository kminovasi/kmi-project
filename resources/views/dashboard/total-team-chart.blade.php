@extends('layouts.app')
@section('title', 'Total team chart | Dashboard')

@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header">
                <h5>Total Team Chart</h5>
            </div>
            <div class="card-body">
                <canvas id="total-team-chart"></canvas>
            </div>
        </div>

        <!-- Tambahkan variabel chartData ke JavaScript -->
        <script>
            const chartData = @json($chartData);
        </script>
    </div>
@endsection

@vite(['resources/js/totalTeamChart.js'])
