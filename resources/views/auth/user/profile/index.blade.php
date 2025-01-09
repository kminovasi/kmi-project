@extends('layouts.app')
@section('title', 'Profile')
@section('content')
    <!-- Your content for the home page here -->
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row d-flex align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3 ">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="user"></i></div>
                            Profile
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

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
                <x-dashboard.innovator.schedule-event :activeEvents="$activeEvents" />
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
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <!-- Account page navigation-->

        <div class="row">
            <div class="col-xl-12">
                <!-- Account details card-->
                <div class="card mb-4">
                    <div class="card-header">Profile Details</div>
                    <div class="card-body">
                        <form>
                            <!-- Form Group (email address)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="dataName">Name</label>
                                <input class="form-control" id="dataName" type="email"
                                    placeholder="Enter your email address" value="{{ $name }}" disabled />
                            </div>
                            <!-- Form Group (email address)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="dataEmail">Email address</label>
                                <input class="form-control" id="dataEmail" type="email"
                                    placeholder="Enter your email address" value="{{ $email }}" disabled />
                            </div>
                            <!-- Form Row        -->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (conmpany name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataCompany">Company name</label>
                                    <input class="form-control" id="dataCompany" type="text"
                                        placeholder="Enter your organization name" value="{{ $company }}" disabled />
                                </div>
                                <!-- Form Group (position)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataPosition">Position</label>
                                    <input class="form-control" id="dataPosition" type="text"
                                        placeholder="Enter your location" value="{{ $position }}" disabled />
                                </div>
                            </div>
                            <!-- Form Row        -->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (diroktorate name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataDirectorate">Directorate name</label>
                                    <input class="form-control" id="dataDirectorate" type="text"
                                        placeholder="Enter your organization name" value="{{ $directorate }}" disabled />
                                </div>
                                <!-- Form Group (department)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataDepartment">Department</label>
                                    <input class="form-control" id="dataDepartment" type="text"
                                        placeholder="Enter your location" value="{{ $department }}" disabled />
                                </div>
                            </div>
                            <!--form row  -->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (unit name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataUnit">Unit</label>
                                    <input class="form-control" id="dataUnit" type="text"
                                        placeholder="Enter your organization name" value="{{ $unit }}" disabled />
                                </div>
                                <!-- Form Group (section)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataSection">Section</label>
                                    <input class="form-control" id="dataSection" type="text"
                                        placeholder="Enter your location" value="{{ $section }}" disabled />
                                </div>
                            </div>
                            <!-- Form Group (manager)-->

                            <div class="mb-3">
                                <label class="small mb-1" for="dataManager">Job Level</label>
                                <input class="form-control" id="dataManager" value="{{ $jobLevel }}" disabled />
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="dataManager">Manager</label>
                                <input class="form-control" id="dataManager" value="{{ $manager }}" disabled />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
