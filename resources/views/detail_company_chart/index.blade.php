@extends('layouts.app')

@section('title', 'Detail Company Chart')

@section('content')
    @vite(['resources/css/detailCompanyChart.css'])

    <x-header-content :title="'Chart Total Innovator per Kategori Tahun : ' . $selectedYear">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#yearFilterModal">
            <i class="fas fa-filter me-2"></i>Filter Tahun
        </button>
    </x-header-content>
    <div class="container mt-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-4 text-md-end">

            </div>
        </div>

        <div class="row card p-3">
            @foreach ($companies as $index => $company)
                <div class="col-md-10 mb-4">
                    <div class="accordion" id="accordionCompany{{ $company->id }}">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $company->id }}">
                                <button class="accordion-button {{ $index != 0 ? 'collapsed' : '' }}" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $company->id }}"
                                    aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                    aria-controls="collapse{{ $company->id }}">
                                    {{ $company->company_name }}
                                </button>
                            </h2>
                            <div id="collapse{{ $company->id }}" class="accordion-collapse collapse "
                                aria-labelledby="heading{{ $company->id }}"
                                data-bs-parent="#accordionCompany{{ $company->id }}">
                                <div class="accordion-body">
                                    <a href="{{ route('detail-company-chart-show', ['id' => $company->id]) }}" class="text-decoration-none">
                                        <div class="card company-card" data-company-id="{{ $company->id }}"
                                            data-company-code="{{ $company->company_code }}">
                                            <div class="card-header bg-light">
                                                <h5 class="card-title mb-0 text-center">{{ $company->company_name }}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="text-center mb-3">
                                                    <img src="{{ $company->logo_url }}"
                                                        alt="Logo {{ $company->company_name }}" class="img-fluid company-logo">
                                                </div>
                                                <div class="chart-container">
                                                    <x-detail-company-chart.innovator-chart :selected-year="$selectedYear" :company-id="$company->id"
                                                        :company-code="$company->company_code" />
                                                </div>
                                            </div>
                                            <div class="p-3">
                                                <a class="btn btn-primary" href="{{route('detail-company-chart-show', ['id' => $company->id])}}">Lihat Detail</a>
                                                <button class="btn btn-success export-excel"
                                                    data-company-id="{{ $company->id }}">Export to Excel</button>
                                                <button class="btn btn-danger export-pdf"
                                                    data-company-id="{{ $company->id }}">Export to PDF</button>
                                            </div>
                                        </div>
                                    </a>
                                </div>
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
            cursor: pointer;
        }

        .company-card:hover {
            transform: translateY(-5px);
        }
    </style>
@endpush

@vite(['resources/js/company/detailCompanyChart.js'])
@vite(['resources/js/company/exportExcel.js'])
@vite(['resources/js/company/exportPdf.js'])
