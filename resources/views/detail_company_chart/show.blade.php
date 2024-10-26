@extends('layouts.app')

@section('title', 'Detail ' . $company->company_name)

@vite(['resources/css/detailCompanyChart.css'])

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Dashboard: <span class="company-name">{{ $company->company_name }}</span></h2>
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <x-detail-company-chart.paper-count :company-id="$company->id" />
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 mt-4">
                <div class="innovator-card">
                    <h3 class="innovator-title">Data Inovator</h3>
                    <div class="innovator-stat">
                        <span class="innovator-label">Total Inovator</span>
                        <span class="innovator-value">{{ $totalInnovators }}</span>
                    </div>
                    <div class="innovator-gender">
                        <div class="gender-stat">
                            <div class="gender-value">{{ $maleCount }}</div>
                            <div class="gender-label">Laki-laki</div>
                        </div>
                        <div class="gender-stat">
                            <div class="gender-value">{{ $femaleCount }}</div>
                            <div class="gender-label">Perempuan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-4 col-sm-4 col-xs-6">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                    Filter bedasarkan unit organisasi
                </button>
            </div>
        </div>
        <x-detail-company-chart.filter-by-organization-unit :organization-unit="$organizationUnit" :company-id="$company->id" />
        <div class="row mt-4">
            <div class="col-lg-6 col-md-12">
                <x-detail-company-chart.idea-and-innovation-chart :organization-unit="$organizationUnit" :company-id="$company->id" />
            </div>
            <div class="col-lg-6 col-md-12">
                <x-detail-company-chart.innovator-organization :organization-unit="$organizationUnit" :company-id="$company->id" />
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-6 col-md-12">
                <x-detail-company-chart.benefit-directorate :company-id="$company->id" />
            </div>
        </div>
    </div>
    @vite(['resources/js/company/companyDashboardChart.js'])
@endsection
