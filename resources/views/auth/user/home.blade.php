<!-- resources/views/home.blade.php -->
@extends('layouts.app')
@section('title', 'Dashboard')
@section('css')
    <style>
        .bgBase1 {
            min-height: 100vh;
        }

        @media (max-width: 767.98px) {
            .container-xl {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            h4 {
                font-size: 1.25rem;
                margin-top: 1.5rem;
            }
        }

        /* Tablet adjustments */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .dashboard-section {
                margin-bottom: 1.5rem;
            }
        }

        /* Better spacing for all devices */
        .dashboard-section {
            margin-bottom: 2rem;
        }

        /* Card adjustments */
        .card {
            height: 100%;
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('content')
    <div class="bgBase1 p-2 pb-4">
        <x-dashboard.header :year="$year" />

        <!-- Main page content-->
        <div class="container">
            <!-- Main Dashboard Content -->
            <div class="row"> <!-- Added g-4 for better gap spacing -->
                <!-- Left Column - Innovation Data -->
                <div class="col-12 col-lg-6 col-md-8 col-sm-10 dashboard-section">
                    <!-- Innovation Cards -->
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-header bg-gradient-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 fw-bold text-white">Total Data Inovasi Terkirim</h5>
                                    @if(Auth::user()->role == 'Superadmin')
                                    <button class="btn btn-sm btn-white" data-bs-toggle="modal" data-bs-target="#companyFilter">Filter</button>
                                    @else
                                    <i class="bi bi-bar-chart-line text-white" style="font-size: 1.7rem"></i>
                                    @endif
                                </div>
                            </div>
                            <div id="dashboard-card-content" class="p-2 pt-3 mx-auto">
                            @include('components.dashboard.filtered_dashboard_card')
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <x-dashboard.innovator.total-innovator-by-band-level :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode"/>
                    </div>
                </div>

                <!-- Right Column - Benefits -->
                <div class="col-12 col-lg-6 col-md-8 dashboard-section">
                    <!-- Benefit Section -->
                    <x-dashboard.benefit :year="$year" :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode" />

                    <div class="mb-3">
                        <x-dashboard.total-financial-benefit-card :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode" />
                    </div>
                    
                    <div class="mb-3">
                        <x-dashboard.total-non-financial-benefit-card :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode" />
                    </div>
                </div>
            </div>
            
            @if ($isSuperadmin)
                <x-dashboard.total-team-card />
            @else
                <x-dashboard.internal.total-team-card />
            @endif
            
            <div class="mt-3">
                <x-dashboard.innovator.innovator-ranking :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode" />
            </div>
            
            @if(Auth::user()->role == 'Superadmin')
            <!-- Bottom Section - Semen -->
            <div class="row mt-4">
                <div class="col-12">
                    <x-dashboard.innovation.cement-innovation-chart />
                    <x-dashboard.innovation.non-cement-innovation-chart />
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Modal Filter Perusahaan -->
    <div class="modal fade" id="companyFilter" tabindex="-1" aria-labelledby="companyFilterLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="companyFilterLabel">Filter Perusahaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label" for="filter-company">Perusahaan</label>
                  <select id="filter-company" name="filter-company" class="form-select">
                    <option selected>-- Pilih Perusahaan --</option>
                    @foreach($listCompany as $company)
                    <option value="{{ $company->company_code }}">{{ $company->company_name }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
              </div>
            </div>
        </div>
    </div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('filter-company').addEventListener('change', function () {
            const selectedCompany = this.value;

            fetch(`/dashboard/filter-dashboard-company?company_code=${selectedCompany}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    return response.json(); // hanya kalau memang JSON
                })
                .then(data => {
                    
                    document.getElementById('dashboard-card-content').innerHTML = data.html;
                    
                    document.querySelectorAll('.list-paper-link').forEach(link => {
                        const baseUrl = link.getAttribute('href');
                        let url = new URL(baseUrl, window.location.origin);
                    
                        // Hapus parameter company_code sebelumnya, jika ada
                        url.searchParams.set('company_code', selectedCompany);
                    
                        link.href = url.toString(); // update href
                    });
                    
                    // Coba ambil data dari elemen bawaan halaman (bukan hasil filter)
                    const total = parseInt(document.getElementById('totalInnovators').textContent.replace(/\D/g, ''));
                    const male = parseInt(document.getElementById('totalInnovatorsMale').textContent.replace(/\D/g, ''));
                    const female = parseInt(document.getElementById('totalInnovatorsFemale').textContent.replace(/\D/g, ''));
                    const outsource = parseInt(document.getElementById('totalInnovatorsOutsource').textContent.replace(/\D/g, ''));
                    renderInnovatorChart(total, male, female, outsource);
                    
                })
                .catch(err => {
                    console.error('Gagal fetch data:', err);
                    alert('Gagal mengambil data. Apakah Anda sudah login?');
                });
            
        });
    });
</script>

<script src="{{ asset('build/assets/benefitChart-c7442a10.js') }}" type="module"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

