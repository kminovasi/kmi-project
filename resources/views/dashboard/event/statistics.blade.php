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
    <div class="container py-2"> <!-- Kurangi padding -->
        <!-- Main Card for Innovator and Benefits -->
        <div class="card mb-3 p-3 shadow-lg"> <!-- Kurangi margin dan padding -->
            <div class="card-body">
                <!-- Innovator Card Section -->
                <div class="row mb-2"> <!-- Kurangi margin -->
                    <x-dashboard.event.innovator-card :event-id="$eventId" />
                </div>

                <!-- Cards for Total Benefit and Total Potential Benefit -->
                <div class="row">
                    <!-- Card for Total Benefit Company Chart (Left) -->
                    <div class="col-md-6 mb-1"> <!-- Kurangi margin bawah -->
                        <x-dashboard.event.total-benefit-company-chart :event-id="$eventId" />
                    </div>

                    <!-- Card for Total Potential Benefit Company Chart (Right) -->
                    <div class="col-md-6 mb-1"> <!-- Kurangi margin bawah -->
                        <x-dashboard.event.total-potential-benefit-company-chart :event-id="$eventId" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-1"> <!-- Kurangi padding -->
        <x-dashboard.event.total-innovator-categories :eventId="$eventId" />
    </div>

    <!-- Grafik & Visualisasi -->
    <div class="row justify-content-center text-center m-auto">
        @if($event_type === 'group' || $event_type === 'internal' || $event_type === 'national' || $event_type === 'international')
        <div class="col-md-12 mb-4">
            <x-dashboard.event.total-team-company-chart :event-id="$eventId" />
        </div>
        @else
        <div class="col-md-12 mb-4">
            <x-dashboard.event.total-innovator-organization :eventId="$eventId" :organizationUnit="$organizationUnit" />
        </div>
        @endif

    </div>

    <!-- Informasi Tambahan -->
    <div class="container py-2"> <!-- Kurangi padding -->
        <div class="card mb-3 p-3 shadow-lg"> <!-- Kurangi margin dan padding -->
            <x-dashboard.event.total-innovator-stages :event-id="$eventId" />
        </div>
    </div>
</div>
@endsection
