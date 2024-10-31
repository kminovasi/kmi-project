@extends('layouts.app')

@section('title', 'Detail Company Chart')

@section('content')
    @vite(['resources/css/detailCompanyChart.css'])

    <div class="container mt-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h2 class="mb-0">Chart Total Innovator per Kategori</h2>
                <p class="text-muted">Tahun {{ $selectedYear }}</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#yearFilterModal">
                    <i class="fas fa-filter me-2"></i>Filter Tahun
                </button>
            </div>
        </div>

        <div class="row">
            @foreach ($companies as $company)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm company-card" data-company-id="{{ $company->id }}"
                        data-company-code="{{ $company->company_code }}">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0 text-center">{{ $company->company_name }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="{{ $company->logo_url }}" alt="Logo {{ $company->company_name }}"
                                    class="img-fluid company-logo">
                            </div>
                            <div class="chart-container">
                                <x-detail-company-chart.innovator-chart :selected-year="$selectedYear" :company-id="$company->id"
                                    :company-code="$company->company_code" />
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Filter Tahun -->
    <div class="modal fade" id="yearFilterModal" tabindex="-1" aria-labelledby="yearFilterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="yearFilterModalLabel">Filter Berdasarkan Tahun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="filterForm" action="{{ route('detail-company-chart') }}" method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="year" class="form-label">Pilih Tahun:</label>
                            <select name="year" id="year" class="form-select">
                                <option value="">Pilih Tahun</option>
                                @foreach ($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('styles')
    <style>
        .company-logo {
            max-height: 80px;
            object-fit: contain;
        }

        .chart-container {
            height: 250px;
        }

        .company-card {
            height: 100%;
            transition: transform 0.2s;
        }

        .company-card:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush

@vite(['resources/js/company/detailCompanyChart.js'])
