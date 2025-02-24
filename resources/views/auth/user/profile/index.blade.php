<style>
    .btn-download {
        width: 75%;
    }

    .btn-form {
        border-top: 1px dotted #ccc;
        text-align: center;
    }
</style>

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
                            Profil
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-4">
        <div class="row justify-content-around">
            <!-- Bagian Profil -->
            <div class="col-md-6 col-sm-10 col-xs-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-center bg-gradient-primary text-white">
                        <h5 class="mb-0 text-white">Profil Anda</h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ asset('images/default-profile.png') }}" alt="Foto Profil" class="img-thumbnail mb-3"
                            style="width: 100px; height: 100px;">
                        <h5 class="card-title">{{ $user->name }}</h5>
                        <p class="card-text"><strong>Posisi:</strong> {{ $user->position_title }}</p>
                        <p class="card-text"><strong>Perusahaan:</strong> {{ $user->company_name }}</p>
                    </div>
                    @if (Auth::user()->role == 'Juri' && $isActiveJudge)
                        <form class="btn-form p-3" action="{{ route('cv.generateCertificate') }}" method="POST">
                            @csrf
                            {{-- Input for Certificate Auto Create --}}
                            <input type="hidden" name="inovasi" value="{{ json_encode($judgeEvents) }}">
                            <input type="hidden" name="employee" value="{{ json_encode($user) }}">
                            <input type="hidden" name="team_rank" value="{{ json_encode($teamRanks) }}">
                            <input type="hidden" name="certificate_type" value="team">

                            <button type="submit" class="btn btn-sm btn-download btn-success mx-5">
                                <i class="dropdown-item-icon mb-1 me-2 fs-2" data-feather="download"></i>
                                Download Sertifikat Juri
                            </button>
                        </form>
                    @endif
                </div>
                <x-profile.list-paper :teamIds="$teamIds" />
            </div>
            <!-- Bagian Tim dan Paper -->
            <div class="col-lg-6 col-md-6 col-sm-10 col-xs-12">
                <x-dashboard.innovator.schedule-event :activeEvents="$activeEvents" />

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
                    <div class="card-header">Detail Pengguna</div>
                    <div class="card-body">
                        <form>
                            <!-- Form Group (email address)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="dataName">Nama lengkap</label>
                                <input class="form-control" id="dataName" type="email"
                                    placeholder="Enter your email address" value="{{ $name }}" disabled />
                            </div>
                            <!-- Form Group (email address)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="dataEmail">Alamat email</label>
                                <input class="form-control" id="dataEmail" type="email"
                                    placeholder="Enter your email address" value="{{ $email }}" disabled />
                            </div>
                            <!-- Form Row        -->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (conmpany name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataCompany">Nama perusahaan</label>
                                    <input class="form-control" id="dataCompany" type="text"
                                        placeholder="Enter your organization name" value="{{ $company }}" disabled />
                                </div>
                                <!-- Form Group (position)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataPosition">Posisi</label>
                                    <input class="form-control" id="dataPosition" type="text"
                                        placeholder="Enter your location" value="{{ $position }}" disabled />
                                </div>
                            </div>
                            <!-- Form Row        -->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (diroktorate name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataDirectorate">Nama irektorat</label>
                                    <input class="form-control" id="dataDirectorate" type="text"
                                        placeholder="Enter your organization name" value="{{ $directorate }}" disabled />
                                </div>
                                <!-- Form Group (department)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="dataDepartment">Departemen</label>
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
