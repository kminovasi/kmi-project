<!-- resources/views/home.blade.php -->
@extends('layouts.app')
@section('title', 'Dashboard')
@section('css')
    <style>
        .bgBase1 {
            background-color: #f8f9fa;
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
    <div class="bgBase1">
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
                            <div class="card-header bg-gradient-primary ">
                                <h5 class="card-title mb-0 fw-bold text-white">Total Data Inovasi Tersubmit </h5>
                            </div>
                            <div class="p-3">
                            <x-dashboard.card :breakthrough-innovation="$breakthroughInnovation" :detail-breakthrough-innovation-management="$detailBreakthroughInnovationManagement" :incremental-innovation="$incrementalInnovation" :detail-incremental-innovation-g-k-m-office="$detailIncrementalInnovationGKMOffice"
                                :detail-incremental-innovation-p-k-m-office="$detailIncrementalInnovationPKMOffice" :detail-incremental-innovation-s-s-plant="$detailIncrementalInnovationSSPlant" :idea-box="$ideaBox" :detail-idea-box-idea="$detailIdeaBoxIdea" :detail-breakthrough-innovation-p-b-b="$detailBreakthroughInnovationPBB"
                                :detail-breakthrough-innovation-t-p-p="$detailBreakthroughInnovationTPP" :detail-incremental-innovation-p-k-m-plant="$detailIncrementalInnovationPKMPlant" :total-innovators="$totalInnovators" :total-innovators-male="$totalInnovatorsMale"
                                :total-innovators-female="$totalInnovatorsFemale" :total-active-events="$totalActiveEvents" />
                            </div>
                        </div>
                    </div>

                    <!-- Innovation Status Total -->
                    {{-- <div>
                        <x-dashboard.innovation-status-total :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode" />
                    </div> --}}
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
                    <div class="mb-4">
                        <h4 class="mb-3" style="background-color: red; color: white; padding: 17px;">Akumulasi Total Benefit</h4>
                        <x-dashboard.benefit :year="$year" :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode" />
                    </div>

                    <div class="mb-3">
                        <x-dashboard.total-financial-benefit-card />
                    </div>

                    <!-- Total Value Custom Benefit -->
                    <div>
                        <x-dashboard.total-value-custom-benefit :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode" />
                    </div>
                </div>
            </div>

            <!-- Bottom Section - Semen -->
            <div class="row mt-4">
                <div class="col-12">
                    <x-dashboard.semen :year="$year" :is-superadmin="$isSuperadmin" :user-company-code="$userCompanyCode" />
                </div>
            </div>
        </div>
    </div>
@endsection

@vite(['resources/js/benefitChart.js'])
