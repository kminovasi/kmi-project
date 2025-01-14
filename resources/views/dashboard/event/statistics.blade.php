
@extends('layouts.app')

@section('title', 'Dashboard Event')

@section('content')
<div class="container mt-4">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12 text-center">
            <h2 class="fw-bold text-primary">{{ $eventName }}</h2>
            <p class="text-muted fs-5">Statistik & Analitik untuk Event</p>
        </div>
    </div>


    <!-- Ringkasan Statistik -->
    <div class="container py-3">
        <!-- Main Card for Innovator and Benefits -->
        <div class="card mb-4 p-4 shadow-lg">
            <div class="card-body">
                <!-- Innovator Card Section -->
                <div class="row mb-3">
                    <x-dashboard.event.innovator-card :event-id="$eventId" />
                </div>

                <!-- Cards for Total Benefit and Total Potential Benefit -->
                <div class="row">
                    <!-- Card for Total Benefit Company Chart (Left) -->
                    <div class="col-md-6 mb-2">
                        <x-dashboard.event.total-benefit-company-chart :event-id="$eventId" />
                    </div>

                    <!-- Card for Total Potential Benefit Company Chart (Right) -->
                    <div class="col-md-6 mb-2">
                        <x-dashboard.event.total-potential-benefit-company-chart :event-id="$eventId" />
                    </div>
                </div>
            </div>
        </div>
    </div>




    <!-- Grafik & Visualisasi -->
    <div class="row justify-content-center text-center m-auto">
        <div class="row mb-1">
            <x-dashboard.event.total-innovator-organization :eventId="$eventId" :organizationUnit="$organizationUnit" />
        </div>
        <div class="row mb-1">
            <x-dashboard.event.total-innovator-categories :eventId="$eventId" />
        </div>

    </div>

    <!-- Informasi Tambahan -->
    <div class="row mb-1">
        <div class="col-12">
            <x-dashboard.event.total-innovator-stages :event-id="$eventId" />

        </div>
    </div>
</div>
@endsection
