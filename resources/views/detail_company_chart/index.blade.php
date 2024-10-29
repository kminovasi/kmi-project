@extends('layouts.app')

@section('title', 'Detail Company Chart')

@section('content')
    @vite(['resources/css/detailCompanyChart.css'])
    <div class="container">
        <div class="row mt-2">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <h4 class="mt-3">Chart total innovator per kategori tahun {{ $selectedYear }}</h4>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-6 text-right">
                <!-- Tombol untuk membuka modal filter -->
                <button type="button" class="btn btn-primary btn-sm mt-2" data-bs-toggle="modal"
                    data-bs-target="#yearFilterModal">
                    Filter Tahun
                </button>
            </div>
        </div>

        <!-- Modal Filter Tahun -->
        <div class="modal fade" id="yearFilterModal" tabindex="-1" aria-labelledby="yearFilterModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="yearFilterModalLabel">Filter Berdasarkan Tahun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="filterForm" action="{{ route('detail-company-chart') }}" method="GET">
                            <div class="form-group">
                                <label for="year">Pilih Tahun:</label>
                                <select name="year" id="year" class="form-control">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($availableYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" form="filterForm">Terapkan Filter</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach ($companies as $company)
                <div class="col-md-4 col-sm-6 col-xs-12 mb-4">
                    <div class="card company-card" data-company-id="{{ $company->id }}"
                        data-company-code="{{ $company->company_code }}">
                        <div class="card-header text-center">
                            <h5>{{ $company->company_name }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ $company->logo_url }}" alt="Logo {{ $company->company_name }}"
                                class="img-fluid mb-3" style="max-height: 100px;">
                            <div class="chart-placeholder" style="height: 200px;">
                                <x-detail-company-chart.innovator-chart :selected-year="$selectedYear" :company-id="$company->id"
                                    :company-code="$company->company_code" />
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@vite(['resources/js/company/detailCompanyChart.js']);
