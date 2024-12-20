@extends('layouts.app')

@section('title', 'Dashboard Inovator')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Bagian Profil -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header text-center bg-primary text-white">
                    <h5 class="mb-0">Profil Anda</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('images/default-profile.png') }}" alt="Foto Profil" class="img-thumbnail mb-3" style="width: 100px; height: 100px;">
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="card-text"><strong>Posisi:</strong> {{ $user->position_title }}</p>
                    <p class="card-text"><strong>Perusahaan:</strong> {{ $user->company_name }}</p>
                </div>
            </div>
        </div>

        <!-- Bagian Tim dan Paper -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Tim & Paper Anda</h5>
                </div>
                <div class="card-body">
                    @if($teams->isEmpty())
                        <p class="text-muted">Tidak ada tim atau paper yang ditemukan.</p>
                    @else
                        <div class="list-group">
                            @foreach($teams as $team)
                                <a href="#" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">{{ $team->team_name }}</h5>
                                        <span class="badge bg-info text-dark">{{ $team->status_lomba }}</span>
                                    </div>
                                    <small>Perusahaan: {{ $team->company_code }}</small>
                                    @if($team->paper)
                                        <div class="mt-2">
                                            <h6>Paper: {{ $team->paper->innovation_title }}</h6>
                                            <p class="text-muted mb-1">{{ $team->paper->abstract }}</p>
                                            <span class="badge {{ getStatusBadgeClass($team->paper->status) }}">{{ $team->paper->status }}</span>
                                        </div>
                                    @else
                                        <p class="text-muted mt-2">Tidak ada paper yang diajukan untuk tim ini.</p>
                                    @endif
                                    @foreach($team->pvtEventTeams as $eventTeam)
                                        @if($eventTeam->event)
                                            <small class="d-block text-muted">Event: {{ $eventTeam->event->event_name }}</small>
                                        @endif
                                    @endforeach
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
