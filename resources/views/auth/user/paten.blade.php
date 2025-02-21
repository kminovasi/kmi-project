@extends('backend.app')
@section('title', 'Paten')
@section('content')
    <!-- Your content for the home page here -->
    <header class="page-header page-header-dark bg-gradient-primary-to-secondary pb-10">
        <div class="container-xl px-4">
            <div class="page-header-content pt-4">
                <div class="row align-items-center justify-content-between">
                    <div class="col-auto mt-4">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="activity"></i></div>
                            Paten
                        </h1>
                        @php
                            $formattedDateTime = now()->locale('id')->format('l · F d, Y · h:i A');
                        @endphp
                        <div class="page-header-subtitle">{{ $formattedDateTime }}</div>
                    </div>
                    <div class="col-12 col-xl-auto mt-4">
                        <div class="input-group input-group-joined border-0" style="width: 16.5rem">
                            <span class="input-group-text"><i class="text-primary" data-feather="calendar"></i></span>
                            <input class="form-control ps-0 pointer" id="litepickerRangePlugin" placeholder="Select date range..." />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-xl px-4 mt-n10">
        <div class="row">
            <div class="col-xxl-4 col-xl-12 mb-4">
                <div class="card h-100">
                    <div class="card-body h-100 p-5">
                        <div class="row align-items-center">
                            <div class="col-xl-8 col-xxl-12">
                                <div class="text-center text-xl-start text-xxl-center mb-4 mb-xl-0 mb-xxl-4">
                                    <h1 class="text-primary">Selamat Datang di KMI Dashboard</h1>
                                    <p class="text-gray-700 mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                                </div>
                            </div>
                            <div class="col-xl-4 col-xxl-12 text-center"><img class="img-fluid" src="{{asset('template/dist/assets/img/illustrations/at-work.sv')}}g" style="max-width: 26rem" /></div>
                        </div>
                    </div>
                </div>
            </div>                   
        </div>
    </div>
@endsection
