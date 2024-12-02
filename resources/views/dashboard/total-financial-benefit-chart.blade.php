@extends('layouts.app')
@section('title', 'Total Benefit Chart | Dashboard')

@section('content')
    <div class="container mt-3">
        <div class="card">
            <div class="card-header" style="background-color: #eb4a3a">
                <h5 class="text-white">Total Financial Benefit per Perusahaan </h5>
            </div>
            <div class="card-body">
                <canvas id="total-benefit-chart"></canvas>
            </div>
        </div>
        <x-dashboard.potential-benefit-total-chart />
    </div>
@endsection

@vite(['resources/js/totalBenefitChart.js'])

<script>
    const chartDataTotalBenefit = @json($chartDataTotalBenefit);
</script>
