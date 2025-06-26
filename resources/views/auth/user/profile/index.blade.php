<style>
    .btn-download {
        width: 75%;
    }

    .btn-form {
        border-top: 1px dotted #ccc;
        text-align: center;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

@extends('layouts.app')
@section('title', 'Profil')
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
    
    <div class="mb-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                {{ session('success') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="container mt-4">
        <div class="row justify-content-around">
            <!-- Bagian Profil -->
            <div class="col-md-6 col-sm-10 col-xs-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary bg-gradient d-flex justify-content-between">
                        <h5 class="mb-0 text-white text-center">Profil Anda</h5>
                        <div>
                            <button class="text-end btn btn-sm btn-danger d-none" id="btnCancelEditProfile">Batal</button>
                            <button class="text-end btn btn-sm btn-secondary" id="btnEditProfile">Edit Poto Profile</button>
                        </div>
                    </div>
                    <div class="card-body text-center">
                        <div style="max-width: 100px; height: auto;" class="text-center w-100 mx-auto">
                            <img src="{{ route('query.getFile') }}?directory={{ urlencode($profilePicture) }}" alt="Foto Profil" class="rounded mx-auto d-block mb-3"
                                style="max-width: 100%; height: auto;">
                        </div>
                        <form id="updatePhotoProfile" method="POST" class="d-none" action="{{ route('profile.updateProfilePicture', ['employeeId' => $user->employee_id]) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <input type="file" class="form-control w-50 mx-auto" name="photo_profile" required accept=".jpg,.png,.jpeg" />
                        </form>
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
                    <div class="card-header bg-primary bg-gradient text-white">Detail Pengguna</div>
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
                        <form id="changePassword" action="{{ route('profile.updatePassword', ['employeeId' => $userId]) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3 position-relative">
                                <label class="small mb-1">Input Password</label>
                                <input 
                                    id="passwordInput" 
                                    type="password" 
                                    name="password" 
                                    class="form-control pe-5" 
                                    placeholder="Inputkan Password Baru"
                                    readonly
                                />
                                <span 
                                    class="position-absolute top-50 end-0 translate-middle-y mt-2 me-3" 
                                    style="cursor: pointer;" 
                                    id="togglePassword"
                                >
                                    <i class="bi bi-eye-slash-fill fs-1 d-none" id="togglePasswordIcon"></i>
                                </span>
                            </div>
                            <div class="mb-3">
                                <button type="button" id="toggleInput" class="btn btn-sm btn-danger">Edit Password</button>
                                <button type="submit" id="submitButton" class="btn btn-sm btn-primary" disabled>Simpan Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('toggleInput');
            const passwordInput = document.getElementById('passwordInput');
            const submitBtn = document.getElementById('submitButton');
            const icon = document.getElementById('togglePasswordIcon');
        
            toggleBtn.addEventListener('click', function () {
                const isReadonly = passwordInput.hasAttribute('readonly');
        
                if (isReadonly) {
                    passwordInput.removeAttribute('readonly');
                    passwordInput.focus();
                    toggleBtn.textContent = 'Kunci Password';
                    icon.classList.remove('d-none');
                    submitBtn.disabled = false;
                } else {
                    passwordInput.setAttribute('readonly', true);
                    toggleBtn.textContent = 'Edit Password';
                    icon.classList.add('d-none');
                    submitBtn.disabled = true;
                }
            });
            
            const toggle = document.getElementById('togglePassword');
            const input = document.getElementById('passwordInput');
            
            toggle.addEventListener('click', function () {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.className = isPassword ? 'fs-1 bi bi-eye-fill' : 'fs-1 bi bi-eye-slash-fill';
            });
            
            const btnEditPhotoProfile = document.getElementById('btnEditProfile');
            const formUpdatePhotoProfile = document.getElementById('updatePhotoProfile');
            const cancelEditPhotoProfile = document.getElementById('btnCancelEditProfile');
            
            btnEditPhotoProfile.addEventListener('click', function () {
                const isHidden = formUpdatePhotoProfile.classList.contains('d-none');
            
                if (isHidden) {
                    formUpdatePhotoProfile.classList.remove('d-none');
                    cancelEditPhotoProfile.classList.remove('d-none');
                    btnEditPhotoProfile.textContent = 'Simpan Foto Profile';
                } else {
                    
                    if (!formUpdatePhotoProfile.querySelector('input[type="file"]').files.length) {
                        alert('Silakan pilih file terlebih dahulu.');
                        return;
                    }

                    formUpdatePhotoProfile.submit();
                    btnEditPhotoProfile.textContent = 'Edit Foto Profile';
                    formUpdatePhotoProfile.classList.add('d-none');
                    cancelEditPhotoProfile.classList.add('d-none');
                }
            });
            
            cancelEditPhotoProfile.addEventListener('click', function () {
                formUpdatePhotoProfile.classList.add('d-none');
                cancelEditPhotoProfile.classList.add('d-none');
                btnEditPhotoProfile.textContent = 'Edit Foto Profile';
            });
        });
    </script>
@endsection
