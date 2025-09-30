<style>
    .btn-download {
        width: 50%;
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
                            Profil Pengguna
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
                    <!--@if (Auth::user()->role == 'Juri' && $isActiveJudge && $judgeEvents->count())-->
                    <!--    <div class="dropdown btn-form py-2">-->
                    <!--        <button class="btn btn-download btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">-->
                    <!--            Lihat Sertifikat Juri-->
                    <!--        </button>-->
                    <!--        <ul class="dropdown-menu">-->
                    <!--            @foreach ($judgeEvents as $event)-->
                    <!--                <li>-->
                    <!--                    <form action="{{ route('cv.generateCertificate') }}" method="POST" class="px-3 py-2">-->
                    <!--                        @csrf-->
                    <!--                         <input type="hidden" name="certificate_type" value="judge">-->
                    <!--                        <input type="hidden" name="event_id" value="{{ $event->event_id }}">-->
                    <!--                        {{-- <input type="hidden" name="employee" value="{{ $user }}"> --}}-->
                    <!--                        <input type="hidden" name="employee" value="{{ $user->employee_id }}">-->
                    <!--                        <button type="submit" class="btn btn-sm btn-link text-start w-100">-->
                    <!--                            {{ $event->event_name . ' Tahun ' . $event->year }}-->
                    <!--                        </button>-->
                    <!--                    </form>-->
                    <!--                </li>-->
                    <!--            @endforeach-->
                    <!--        </ul>-->
                    <!--    </div>-->
                    <!--@endif-->
@if (Auth::user()->role === 'Juri' && $isActiveJudge && $judgeEvents->count())
    @php
        // Saring: TAMPILKAN selain event_id 3 dan 4
        $visibleJudgeEvents = $judgeEvents->filter(function ($e) {
            return !in_array((int) $e->event_id, [3, 4], true);
        });
    @endphp

    @if ($visibleJudgeEvents->count())
        <div class="dropdown btn-form py-2">
            <button class="btn btn-download btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                Lihat Sertifikat Juri
            </button>
            <ul class="dropdown-menu">
                @foreach ($visibleJudgeEvents as $event)
                    <li>
                        <form action="{{ route('cv.generateCertificate') }}" method="POST" class="px-3 py-2">
                            @csrf
                            <input type="hidden" name="certificate_type" value="judge">
                            <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                            <input type="hidden" name="employee" value="{{ $user->employee_id }}">
                            <button type="submit" class="btn btn-sm btn-link text-start w-100">
                                {{ $event->event_name.' Tahun '.$event->year }}
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
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
                    {{-- <div class="card-header bg-primary bg-gradient text-white">Detail Pengguna</div> --}}
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary bg-gradient text-white">
                        <span>Detail Pengguna</span>
                        <div>
                            <button id="btnEditUser" class="btn btn-sm btn-light">
                                <i class="bi bi-pencil-square"></i> Edit
                            </button>
                            <button type="submit" form="formUserDetails" id="btnSaveUser" class="btn btn-sm btn-success d-none">
                                <i class="bi bi-check2-square"></i> Simpan
                            </button>
                            <button id="btnCancelUser" class="btn btn-sm btn-danger d-none">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                        </div>
                    </div>

                    <form id="formUserDetails" method="POST"
                        action="{{ route('profile.updateUserDetails', ['employeeId' => $userId]) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                    {{-- Ringkasan nama & email --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                        <label class="small mb-1">Nama lengkap</label>
                        <input name="name" type="text"
                                class="form-control editable @error('name') is-invalid @enderror"
                                value="{{ old('name', $name) }}" disabled>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                        <label class="small mb-1">Alamat email</label>
                        <input name="email" type="email"
                                class="form-control editable @error('email') is-invalid @enderror"
                                value="{{ old('email', $email) }}" disabled>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Dua kolom --}}
                    <div class="row g-3">
                        {{-- KIRI --}}
                        <div class="col-xl-6">
                        <div class="mb-3">
                            <label class="small mb-1">ID Karyawan</label>
                            <input name="employee_id" type="text" class="form-control editable"
                                value="{{ old('employee_id', $userId) }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Nama Pengguna</label>
                            <input name="username" type="text" class="form-control editable"
                                value="{{ old('username', $user->username ?? '') }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Jabatan</label>
                            <input type="text" class="form-control"
                                value="{{ $position }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Job Level</label>
                            <input type="text" class="form-control" value="{{ $jobLevel }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">ID Atasan</label>
                            <input type="text" class="form-control" value="{{ $user->manager_id ?? '-' }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Kode Perusahaan</label>
                            <input type="text" class="form-control" value="{{ $user->company_code ?? '-' }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Nama Perusahaan</label>
                            <input type="text" class="form-control" value="{{ $company }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Perusahaan Asal</label>
                            <input type="text" class="form-control" value="{{ $user->origin_company_name ?? '-' }}" disabled>
                        </div>
                        </div>

                        {{-- KANAN --}}
                        <div class="col-xl-6">
                        <div class="mb-3">
                            <label class="small mb-1">Nama Direktorat</label>
                            <input type="text" class="form-control" value="{{ $directorate }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Nama Grup Fungsi</label>
                            <input type="text" class="form-control" value="{{ $user->function_group_name ?? '-' }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Nama Departemen</label>
                            <input type="text" class="form-control" value="{{ $department }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Nama Unit</label>
                            <input type="text" class="form-control" value="{{ $unit }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Nama Seksi</label>
                            <input type="text" class="form-control" value="{{ $section }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Sub Seksi</label>
                            <input type="text" class="form-control" value="{{ $user->subsection_name }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Role</label>
                            <input type="text" class="form-control" value="{{ $user->role ?? '-' }}" disabled>
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Jenis Kelamin</label>
                            <select name="gender" class="form-select editable @error('gender') is-invalid @enderror" disabled>
                            <option value="" hidden>Pilih</option>
                            <option value="Laki-laki"  @selected(old('gender', $user->gender)==='Laki-laki')>Laki-laki</option>
                            <option value="Perempuan"  @selected(old('gender', $user->gender)==='Perempuan')>Perempuan</option>
                            </select>
                            @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="small mb-1">Tanggal Lahir</label>
                            <input name="date_of_birth" type="text" class="form-control editable"
                                value="{{ old('date_of_birth', $user->date_of_birth) }}"
                                placeholder="dd-mm-yy"
                                disabled>
                        </div>
                    </div>

                    {{-- Manager (nama) --}}
                    <div class="mt-3">
                        <label class="small mb-1">Manager</label>
                        <input type="text" class="form-control" value="{{ $manager }}" disabled>
                    </div>
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

            document.addEventListener('DOMContentLoaded', () => {
            const btnEdit = document.getElementById('btnEditUser');
            const btnSave = document.getElementById('btnSaveUser');
            const btnCancel = document.getElementById('btnCancelUser');
            const editableFields = document.querySelectorAll('.editable');

            btnEdit.addEventListener('click', () => {
                editableFields.forEach(el => el.removeAttribute('disabled'));
                btnEdit.classList.add('d-none');
                btnSave.classList.remove('d-none');
                btnCancel.classList.remove('d-none');
            });

            btnCancel.addEventListener('click', () => {
                editableFields.forEach(el => el.setAttribute('disabled', true));
                btnEdit.classList.remove('d-none');
                btnSave.classList.add('d-none');
                btnCancel.classList.add('d-none');
            });
        });
    </script>
@endsection
