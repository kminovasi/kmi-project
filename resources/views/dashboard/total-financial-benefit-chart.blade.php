@extends('layouts.app')
@section('title', 'Total Benefit Chart | Dashboard')

@section('content')
    <div class="container mt-3">
        @if ($isSuperadmin)
            <div class="card">
                <div class="card-header" style="background-color: #eb4a3a">
                    <h5 class="text-white">Total Financial Benefit per Perusahaan </h5>
                </div>
                <div class="card-body">
                    <canvas id="total-benefit-chart" style="height: 1500px;"></canvas>
                </div>
            </div>
            <x-dashboard.potential-benefit-total-chart />
            <x-dashboard.financial-benefit-chart-companies />
        @else
            <x-dashboard.financial-benefit-total-chart :is-superadmin="auth()->user()->role === 'Superadmin'" :user-company-code="auth()->user()->company_code" />
            <x-dashboard.potential-benefit-total :is-superadmin="auth()->user()->role === 'Superadmin'" :user-company-code="auth()->user()->company_code" />
        @endif

    </div>
@endsection

@vite(['resources/js/totalBenefitChart.js'])

<script>
    const chartDataTotalBenefit = @json($chartDataTotalBenefit);
</script>
