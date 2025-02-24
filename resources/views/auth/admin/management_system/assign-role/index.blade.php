@extends('layouts.app')
@section('title', 'Manajemen Sistem | Penambahan Role')
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<style type="text/css">
    .active-link {
        color: #ffc004;
        background-color: #e81500;
    }
</style>
@endpush
@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">

            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Role Pengguna
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-primary" href="{{ route('management-system.role.assign.add') }}">
                            <i class="me-1" data-feather="plus"></i>
                            Tambahkan Role
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                        <div class="col-xl-4 mb-4">
                            <!-- Dashboard example card 2-->
                            <a class="card lift h-100 border-start-lg border-start-primary" href="{{route('management-system.role.innovator.index')}}">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="me-3">
                                            <i class="feather-xl text-primary mb-3" data-feather="user"></i>
                                            <h5 class="text-primary">Inovator</h5>
                                            <div class="text-muted small">
                                                Semua karyawan SIG Group merupakan inovator yang mendapatkan akses untuk membuat inovasi, melihat makalah dan informasi didalam aplikasi.
                                                Semua karyawan memiliki default inovator
                                            </div>
                                        </div>
                                        {{-- <img src="assets/img/illustrations/processing.svg" alt="..." style="width: 8rem" /> --}}
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-4 mb-4">
                            <!-- Dashboard example card 1-->
                            <a class="card lift h-100 border-start-lg border-start-success" href="{{route('management-system.role.bod.index')}}">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="me-3">
                                            <i class="feather-xl text-success mb-3" data-feather="user"></i>
                                            <h5 class="text-success">Board Of Director</h5>
                                            <div class="text-muted small">
                                                Direksi yang ditunjuk oleh pengelola inovasi untuk memberikan penilaian hasil dari cauccus juri untuk menentukan pemenang kompetisi.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-4 mb-4">
                            <!-- Dashboard example card 3-->
                            <a class="card lift h-100 border-start-lg border-start-info" href="{{route('management-system.juri')}}">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="me-3">
                                            <i class="feather-xl text-info mb-3" data-feather="user"></i>
                                            <h5 class="text-info">Juri</h5>
                                            <div class="text-muted small">
                                                Karyawan yang ditunjuk oleh pengelola inovasi menjdi juri dalam event tertentu. Juri akan mendapatkan role penilaian dan role inovator
                                                ( melihat makalah dan informasi lainya dalam aplikasi)
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-xl-4 mb-4">
                            <!-- Dashboard example card 2-->
                            <a class="card lift h-100 border-start-lg border-start-secondary" href="{{route('management-system.role.admin.index')}}">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="me-3">
                                            <i class="feather-xl text-secondary mb-3" data-feather="user"></i>
                                            <h5 class="text-secondary">Admin</h5>
                                            <div class="text-muted small">
                                                Pengelola Inovasi masing-masing perusahaan yang akan memnentukan role pada Inovator, Juri,
                                                BOD serta menentukan aturan kegiatan kompetisi inovasi
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @if(Auth::user()->role == 'Superadmin')
                         <div class="col-xl-4 mb-4">
                            <!-- Dashboard example card 2-->
                            <a class="card lift h-100 border-start-lg border-start-warning" href="{{route('management-system.role.superadmin.index')}}">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="me-3">
                                            <i class="feather-xl text-warning mb-3" data-feather="user"></i>
                                            <h5 class="text-warning">Super Admin</h5>
                                            <div class="text-muted small">Pengelola Inovasi Holding</div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@endpush
