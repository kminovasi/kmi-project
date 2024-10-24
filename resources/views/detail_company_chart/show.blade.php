@extends('layouts.app')

@section('title', 'Detail ' . $company->company_name)

@vite(['resources/css/detailCompanyChart.css'])

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Dashboard: <span class="company-name">{{ $company->company_name }}</span></h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-md-10 col-sm-12">
                <x-detail-company-chart.paper-count :company-id="$company->id" />
            </div>
        </div>
    </div>
    @vite(['resources/js/company/companyDashboardChart.js'])
@endsection
