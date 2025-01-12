
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
    <div class="row mb-4">
        <x-dashboard.event.innovator-card :event-id="$eventId" />
    </div>

    <!-- Grafik & Visualisasi -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <x-dashboard.event.total-innovator-organization :eventId="$eventId" :organizationUnit="$organizationUnit" />
        </div>
        <div class="col-md-6 mb-4">
            <x-dashboard.event.total-innovator-categories :eventId="$eventId" />
        </div>
        <div class="col-md-6 mb-4">
            <x-dashboard.event.total-benefit-company-chart :event-id="$eventId" />
        </div>
        <div class="col-md-6 mb-4">
            <x-dashboard.event.total-potential-benefit-company-chart :event-id="$eventId" />
        </div>
    </div>

    <!-- Informasi Tambahan -->
    <div class="row">
        <div class="col-12">
            <x-dashboard.event.total-innovator-stages :event-id="$eventId" />

        </div>
    </div>
</div>
@endsection
