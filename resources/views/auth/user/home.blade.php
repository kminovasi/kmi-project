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
                                    <i class="bi bi-bar-chart-line text-white" style="font-size: 1.7rem"></i> 
                                </div>
                            </div>
                            <div class="p-2 pt-3 mx-auto">
                            <x-dashboard.card :implemented="$implemented" :total-innovators="$totalInnovators" :total-innovators-male="$totalInnovatorsMale"
                                :total-innovators-female="$totalInnovatorsFemale" :total-innovatores-outsource="$totalInnovatoresOutsource" :total-active-events="$totalActiveEvents" :idea-box="$ideaBox" :total-implemented-innovations="$totalImplementedInnovations" :total-idea-box-innovations="$totalIdeaBoxInnovations" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <x-dashboard.innovator.total-innovator-by-band-level :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode"/>
                    </div>

                    <div>
                        @if ($isSuperadmin)
                            <x-dashboard.total-team-card />
                        @else
                            <x-dashboard.internal.total-team-card />
                        @endif
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
@endsection

<script src="{{ asset('build/assets/benefitChart-c7442a10.js') }}" type="module"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

