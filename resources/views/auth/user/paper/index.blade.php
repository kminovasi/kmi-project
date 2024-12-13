@extends('layouts.app')

@section('title', 'Data Makalah - Portal Inovasi')

@push('css')
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <style>
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 20px;
        }

        .dataTables_wrapper .dt-buttons {
            margin-bottom: 20px;
        }

        .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background-color: #f5f5f5;
        }

        .dt-button {
            background-color: #0d6efd !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
            padding: 5px 15px !important;
            margin-right: 5px !important;
        }

        .dt-button:hover {
            background-color: #0b5ed7 !important;
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
                            Data Paper - Innovation Paper
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="container-xl px-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> List Error:
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </header>

    <div class="container-xl px-4 mt-4">
        @include('auth.user.paper.navbar')
        <div class="mb-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <div class="filter-container col-md-4">
                        @if (Auth::user()->role == 'Superadmin')
                            <button class="btn btn-primary btn-sm me-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#filterModal">Filter</button>
                        @endif
                        {{-- @if (Auth::user()->role === 'Superadmin' || Auth::user()->role === 'Admin')
                            <select id="filter-status-inovasi" name="filter-status-inovasi" class="form-select">
                                <option value="Not Implemented">Not Implemented</option>
                                <option value="Progress">Progress</option>
                                <option value="Implemented">Implemented</option>
                            </select>
                        @endif --}}
                    </div>
                </div>
                <table id="datatable-makalah" class="display">
                    <!-- Tabel akan diisi oleh DataTables -->
                </table>
            </div>
        </div>
    </div>

    {{-- modal untuk detail team --}}
    <div class="modal fade" id="detailTeamMember" tabindex="-1" role="dialog" aria-labelledby="detailTeamMemberTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="detailTeamMemberTitle">Detail Team Member</h5> --}}
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">Detail Team</h5>
                                </div>
                                <div class="card-body">
                                    <form id="modal-card-form">
                                        <div class="mb-3">
                                            <label class="form-label" for="facilitator">Fasilitator</label>
                                            <input class="form-control form-control-lg" id="facilitator" type="text"
                                                value="" readonly />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="leader">Leader</label>
                                            <input class="form-control form-control-lg" id="leader" type="text"
                                                value="" readonly />
                                        </div>
                                    </form>
                                </div>
                            </div>


                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">Foto Tim</h5>
                                </div>
                                <div class="card-body text-center">
                                    <img src="" id="idFotoTim" alt="Foto Tim"
                                        class="img-fluid rounded-3 shadow-sm" />
                                </div>
                            </div>

                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">Foto Inovasi Produk</h5>
                                </div>
                                <div class="card-body text-center">
                                    <img src="" id="idFotoInovasi" alt="Foto Inovasi Produk"
                                        class="img-fluid rounded-3 shadow-sm" />
                                </div>
                            </div>

                        </div>
                        <div class="col-md-8">
                            <div class="card shadow-sm mb-3">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">Detail Makalah</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Judul -->
                                    <div class="mb-3">
                                        <div class="fw-bold">Judul</div>
                                        <div class="small mb-0" id="judul"></div>
                                    </div>
                                    <hr>
                                    <!-- Lokasi Implementasi Inovasi -->
                                    <div class="mb-3">
                                        <div class="fw-bold">Lokasi Implementasi Inovasi</div>
                                        <div class="small mb-0" id="inovasi_lokasi"></div>
                                    </div>
                                    <hr>
                                    <!-- Abstrak -->
                                    <div class="mb-3">
                                        <div class="fw-bold">Abstrak</div>
                                        <div class="small mb-0" id="abstrak"></div>
                                    </div>
                                    <hr>
                                    <!-- Permasalahan -->
                                    <div class="mb-3">
                                        <div class="fw-bold">Permasalahan</div>
                                        <div class="small mb-0" id="problem"></div>
                                    </div>
                                    <hr>
                                    <!-- Penyebab Utama -->
                                    <div class="mb-3">
                                        <div class="fw-bold">Penyebab Utama</div>
                                        <div class="small mb-0" id="main_cause"></div>
                                    </div>
                                    <hr>
                                    <!-- Solusi -->
                                    <div class="mb-3">
                                        <div class="fw-bold">Solusi</div>
                                        <div class="small mb-0" id="solution"></div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal untuk filter khusus superadmin --}}
    <div class="modal fade" id="filterModal" role="dialog" aria-labelledby="detailTeamMemberTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <!-- Header -->
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="detailTeamMemberTitle">Filter</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <!-- Body -->
                <div class="modal-body">
                    <form id="filterForm">
                        <!-- Role Filter -->
                        <div class="mb-3">
                            <label for="filter-role" class="form-label fw-semibold">Role</label>
                            <select id="filter-role" name="filter-role" class="form-select">
                                <?php if(auth()->user()->role == 'Admin' || auth()->user()->role == 'Superadmin'): ?>
                                <option value="admin" selected>Admin</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <!-- Company Filter -->
                        <div class="mb-3">
                            <label for="filter-company" class="form-label fw-semibold">Company</label>
                            <select id="filter-company" name="filter-company" class="form-select">
                                @foreach ($data_company as $company)
                                    <option value="{{ $company->company_code }}"
                                        {{ $company->company_code == Auth::user()->company_code ? 'selected' : '' }}>
                                        {{ $company->company_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Status Inovasi Filter -->
                        <div class="mb-3">
                            <label for="filter-status-inovasi" class="form-label fw-semibold">Status Inovasi</label>
                            <select id="filter-status-inovasi" name="filter-status-inovasi" class="form-select">
                                <option value="" selected>-- Pilih Status --</option>
                                <option value="Not Implemented">Not Implemented</option>
                                <option value="Progress">Progress</option>
                                <option value="Implemented">Implemented</option>
                            </select>
                        </div>
                    </form>
                </div>
                <!-- Footer -->
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit" form="filterForm">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>


    {{-- modal untuk approval makalah fasilitator --}}
    <div class="modal fade" id="accFasilitator" tabindex="-1" role="dialog" aria-labelledby="accFasilitatorTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accFasilitatorTitle">Approval Makalah oleh Fasilitator</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="accFasilPaperForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="mb">
                            <select class="form-select" aria-label="Default select example" name="status"
                                id="status_by_fasil" require>
                                <option selected>-</option>
                                <option value="accepted paper by facilitator">accept</option>
                                <option value="rejected paper by facilitator">reject</option>
                            </select>
                        </div>
                        <div class="mb">
                            <label class="mb-1" for="commentFacilitator">Berikan Komentar</label>
                            <textarea name="comment" class="form-control" id="commentFacilitator" cols="30" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-bs-dismiss="modal"> Approval</button>
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal untuk approval benefit fasilitator --}}
    <div class="modal fade" id="accFasilitatorBnefit" tabindex="-1" role="dialog"
        aria-labelledby="accFasilitatorBnefit" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accFasilitatorBnefitTitle">Approval Benefit oleh Fasilitator</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="accFasilBenefitForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <select class="form-select" aria-label="Default select example" name="status"
                        id="change_benefit_by_fasil" require>
                        <option selected>-</option>
                        <option value="accepted benefit by facilitator">accept</option>
                        <option value="rejected benefit by facilitator">reject</option>
                    </select>
                    <div class="modal-body">
                        <div class="mb">
                            <label class="mb-1" for="commentFacilitator">Berikan Komentar</label>
                            <textarea name="comment" class="form-control" id="commentFacilitator" cols="30" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-bs-dismiss="modal"> Approval</button>
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal untuk approval benefit GM --}}
    <div class="modal fade" id="accGM" tabindex="-1" role="dialog" aria-labelledby="accGMTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accGMTitle">Approval Benefit oleh General Manager</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="accGmBenefitForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <select class="form-select" aria-label="Default select example" name="status"
                        id="change_benefit_by_gm" require>
                        <option selected>-</option>
                        <option value="accepted benefit by general manager">accept</option>
                        <option value="rejected benefit by general manager">reject</option>
                    </select>
                    <div class="modal-body">
                        <div class="mb">
                            <label class="mb-1" for="commentGM">Berikan Komentar</label>
                            <textarea name="comment" class="form-control" id="commentGMr" cols="30" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit" data-bs-dismiss="modal"> Approval</button>
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal untuk approval admin --}}
    <div class="modal fade" id="accAdmin" tabindex="-1" role="dialog" aria-labelledby="accAdminTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBenefitTitle">Approval oleh Admin</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="accAdminForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="text" name="evaluatedBy" value="innovation admin" hidden>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="mb-1" for="status_by_admin">Status</label>
                            <select class="form-select" aria-label="Default select example" name="status"
                                id="status_by_admin" require>
                                <option selected>-</option>
                                <option value="accept">accept</option>
                                <option value="reject">reject</option>
                                <option value="replicate">replicate</option>
                                <option value="not complete">not complete</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div id="registEvent">

                            </div>
                        </div>


                        <!-- <input type="text" name="status" value="accept" hidden> -->

                        <div class="mb">
                            <label class="mb-1" for="commentFacilitator">Comment</label>
                            <textarea name="comment" class="form-control" id="commentFacilitator" cols="30" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit" data-bs-dismiss="modal" id="accAdminButton"
                            disabled> Approval</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal untuk upload beberapa dokumen --}}
    <div class="modal fade" id="uploadDocument" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content shadow">
                <!-- Header -->
                <div class="modal-header bg-white">
                    <h5 class="modal-title fw-bold text-dark" id="uploadDocumentTitle">Upload Dokumen Pendukung</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Body -->
                <form id="uploadDocumentForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Hidden Input -->
                        <input type="hidden" name="paper_id" id="paper_id_input" value="">

                        <!-- File Input -->
                        <div class="mb-3">
                            <label for="inputBeberapaDokumen" class="form-label fw-semibold">Pilih File (PDF, Gambar, atau
                                Video)</label>
                            <input type="file" name="document_support[]" class="form-control border-primary"
                                accept=".pdf, .jpg, .jpeg, .png, .mp4, .avi, .mkv" id="inputBeberapaDokumen" multiple>
                            <small class="text-muted">Maksimal 10MB per file.</small>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="modal-footer d-flex justify-content-end">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-upload"></i> Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal show beberapa dokumen --}}
    <div class="modal fade" id="showDocument" tabindex="-1" role="dialog" aria-labelledby="showDocumentTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="showDocumentTitle">Show Dokumen Pendukung</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div id="resultContainer"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal untuk Melihat Komentar -->
    <div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content border-0 shadow">
                <div class="modal-header " style="background-color: #eb4a3a">
                    <h5 class="modal-title text-white fw-bold" id="commentTitle">Comments</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="fw-bold mb-2" for="commentList">All Comments</label>
                        <div id="commentList" class="bg-light p-3 rounded border overflow-auto"
                            style="max-height: 400px;">
                            <!-- List komentar akan dimasukkan di sini melalui JavaScript -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-end">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- modal untuk info update --}}
    <div class="modal fade" id="updateData" tabindex="-1" role="dialog" aria-labelledby="updateDataTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content shadow-sm border-0">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="updateDataTitle">Update Data</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Form -->
                <form id="updateDataTeam" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Modal Body -->
                    <div class="modal-body px-4 py-3">
                        <!-- Innovation Title -->
                        <div class="mb-3">
                            <label for="inputInnovationTitle" class="form-label fw-semibold">Innovation Title</label>
                            <input type="text" class="form-control shadow-sm" id="inputInnovationTitle"
                                name="innovation_title" value="" placeholder="Enter Innovation Title">
                        </div>

                        <!-- Team Name -->
                        <div class="mb-3">
                            <label for="inputTeamName" class="form-label fw-semibold">Team Name</label>
                            <input type="text" class="form-control shadow-sm" id="inputTeamName" name="team_name"
                                value="" placeholder="Enter Team Name">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="inputCategory" class="form-label fw-semibold">Category</label>
                            <select class="form-select shadow-sm" id="inputCategory" name="category">
                                @foreach ($data_category as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Theme -->
                        <div class="mb-3">
                            <label for="inputTheme" class="form-label fw-semibold">Theme</label>
                            <select class="form-select shadow-sm" id="inputTheme" name="theme">
                                @foreach ($data_theme as $theme)
                                    <option value="{{ $theme->id }}">{{ $theme->theme_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Innovation Status -->
                        <div class="mb-3">
                            <label for="inputStatusInovasi" class="form-label fw-semibold">Status Inovasi</label>
                            <select class="form-select shadow-sm" id="inputStatusInovasi" name="status_inovasi">
                                <option value="Not Implemented">Not Implemented</option>
                                <option value="Progress">Progress</option>
                                <option value="Implemented">Implemented</option>
                            </select>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer bg-light border-top-0 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- modal untuk info history --}}
    <div class="modal fade" id="infoHistory" tabindex="-1" role="dialog" aria-labelledby="infoHistoryTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="infoHistoryTitle">History Activity</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <!-- Body -->
                <div class="modal-body">
                    <div class="timeline" id="history">
                        <!-- Example timeline item -->
                        <div class="timeline-item mb-4">
                            <div class="timeline-dot bg-primary"></div>
                            <div class="timeline-content shadow-sm rounded p-3">
                                <div class="fw-bold mb-1">Activity Title</div>
                                <div class="small text-muted">Description of the activity goes here.</div>
                                <div class="small text-muted mt-1">Date: 2024-11-22</div>
                            </div>
                        </div>
                        <!-- Repeat for more items -->
                    </div>
                </div>
                <!-- Footer -->
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal untuk info rollback --}}
    <div class="modal fade" id="rollback" tabindex="-1" role="dialog" aria-labelledby="rolbackTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="rolbackTitle">Rollback</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <!-- Form -->
                <form id="formRollback" method="POST" action="{{ route('paper.rollback', ['id' => ':id']) }}">
                    @csrf
                    <div class="modal-body">
                        <!-- Rollback Option -->
                        <div class="mb-4">
                            <label for="rollback_option" class="form-label fw-semibold">Rollback Option</label>
                            <select class="form-select shadow-sm" name="rollback_option" id="rollback_option">
                                <option value="full_paper">Rollback Paper</option>
                                <option value="benefit">Rollback Benefit</option>
                            </select>
                        </div>
                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="inputCommentRollback" class="form-label fw-semibold">Comment</label>
                            <textarea name="comment" id="inputCommentRollback" rows="5" class="form-control shadow-sm"
                                placeholder="Add your comments here..."></textarea>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal untuk upload step --}}
    <div class="modal fade" id="uploadStep" tabindex="-1" role="dialog" aria-labelledby="uploadDocumentTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="uploadDocumentTitle">Upload Step Document</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="uploadStepForm" class="upload-step-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            {{-- <p id="paper_id_input"></p> --}}
                            <input type="hidden" name="paper_id" id="paper_id_input" value="">
                            <input type="file" name="file_stage" class="form-control" multiple>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary btn-upload-step" type="submit"
                            data-bs-dismiss="modal">Submit</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


<script>
    $(document).ready(function() {
        var dataTable = $('#datatable-makalah').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "dom": 'lBfrtip',
            "buttons": [
                'excel', 'csv', 'pdf'
            ],
            "ajax": {
                "url": "{{ route('query.getmakalah') }}",
                "type": "GET",
                "dataSrc": function(data) {
                    return data.data;
                },
                data: function(d) {
                    d.filterCompany = $('#filter-company').val();
                    d.filterRole = $('#filter-role').val();
                    d.status_inovasi = $('#filter-status-inovasi')
                        .val(); //ambil nilai yg dipilih ke server
                    return d;
                }
            },
            "columns": [{
                "data": "DT_RowIndex",
                "title": "No"
            }, {
                "data": "innovation_title",
                "title": "Innovation Title"
            }, {
                "data": "team_name",
                "title": "Team Name"
            }, {
                "data": "detail_team",
                "title": "Detail Team"
            }, {
                "data": "company_name",
                "title": "Company"
            }, {
                "data": "category_name",
                "title": "Category"
            }, {
                "data": "theme_name",
                "title": "Theme"
            }, {
                "data": "step_1",
                "title": "Step 1"
            }, {
                "data": "step_2",
                "title": "Step 2"
            }, {
                "data": "step_3",
                "title": "Step 3"
            }, {
                "data": "step_4",
                "title": "Step 4"
            }, {
                "data": "step_5",
                "title": "Step 5"
            }, {
                "data": "step_6",
                "title": "Step 6"
            }, {
                "data": "step_7",
                "title": "Step 7"
            }, {
                "data": "step_8",
                "title": "Step 8"
            }, {
                "data": "full_paper",
                "title": "Full Paper"
            }, {
                "data": "benefit",
                "title": "Benefit"
            }, {
                "data": "approval",
                "title": "Approval"
            }, {
                "data": "Dokumen",
                "title": "Dokumen"
            }, {
                "data": "status",
                "title": "Status"
            }, {
                "data": "action",
                "title": "Action"
            }, ],
            "scrollX": true,
            "scrollY": true,
            "stateSave": true,
        });
    });
</script>


@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script type="">
    $(document).ready(function() {
        var dataTable = $('#datatable-makalah').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive" : true,
            "dom": 'lBfrtip',
            "buttons": [
                'excel', 'csv', 'pdf'
            ],
            "ajax": {
                "url": "{{ route('query.getmakalah') }}",
                "type": "GET",
                "dataSrc": function (data) {
                    return data.data;
                },
                data: function (d) {
                     d.filterCompany = $('#filter-company').val();
                     d.filterRole = $('#filter-role').val();
                     d.status_inovasi = $('#filter-status-inovasi').val(); //ambil nilai yg dipilih ke server
                     return d;
                }
            },
            "columns": [
                {"data": "DT_RowIndex", "title": "No"},
                {"data": "innovation_title", "title": "Innovation Title"},
                // {"data": "inovasi_lokasi", "tittle": "Lokasi Inovasi"},
                {"data": "team_name", "title": "Team Name"},
                {"data": "detail_team","title": "Detail Team"},
                {"data": "company_name","title": "Company"},
                {"data": "category_name","title": "Category"},
                {"data": "theme_name","title": "Theme"},
                {"data": "step_1","title": "Step 1"},
                {"data": "step_2","title": "Step 2"},
                {"data": "step_3","title": "Step 3"},
                {"data": "step_4","title": "Step 4"},
                {"data": "step_5","title": "Step 5"},
                {"data": "step_6","title": "Step 6"},
                {"data": "step_7","title": "Step 7"},
                {"data": "step_8","title": "Step 8"},
                {"data": "full_paper","title": "Full Paper"},
                {"data": "benefit","title": "Benefit"},
                {"data": "approval","title": "Approval"},
                {"data": "Dokumen","title": "Dokumen"},
                {"data": "status","title": "Status"},
                {"data": "action","title": "Action"},
            ],
            "scrollX": true,
            "scrollY": true,
            "stateSave": true,
        });

        $('#filter-company').on('change', function () {
            dataTable.ajax.reload();
        });

        $('#filter-role').on('change', function () {
            dataTable.ajax.reload();

            user_role = "{{ Auth::user()->role }}"
            if($('#filter-role').val() == 'admin' && user_role == 'Superadmin'){
                $('#filter-company').removeAttr("disabled");
            }else{
                $("#filter-company").attr("disabled", "disabled");
            }
        });

        $('#filter-status-inovasi').on('change', function() {
            dataTable.ajax.reload();
        });
    });

    // Ambil data tim
    function get_data_on_modal(IdTeam){
        var fotoTim;
        var fotoInovasi;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('query.get_data_member') }}",
            type: "POST",
            data: {
                team_id: IdTeam
            },
            success: function(data) {
                console.log(data);

                if(typeof data.data.member !== 'undefined'){
                    new_div_member = `
                    <div class="mb-3" id="member-card">
                        <label class="mb-1" for="dataName">Team Member</label>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="List">
                            </ul>
                        </div>
                    </div>`;

                    document.getElementById('modal-card-form').insertAdjacentHTML('beforeend', new_div_member);
                    var ul = document.getElementById('List')
                }

                if(typeof data.data.outsource !== 'undefined'){
                    new_div_outsource = `
                    <div class="mb-3" id="outsource-card">
                        <label class="mb-1" for="dataName">Team Member Outsource</label>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="outsource-List">

                            </ul>
                        </div>
                    </div>
                    `;
                    document.getElementById('modal-card-form').insertAdjacentHTML('beforeend', new_div_outsource);
                    var ul_outsource = document.getElementById('outsource-List')
                }

                Object.keys(data.data).forEach(function(indeks) {
                    if(indeks == 'member'){

                        Object.keys(data.data[indeks]).forEach(function(indeks2) {
                            var elemenLi = document.createElement("li");

                            elemenLi.className = "list-group-item";
                            elemenLi.id = indeks + (parseInt(indeks2) + 1);

                            var elemenA = document.createElement("a");
                            elemenA.textContent = data.data[indeks][indeks2].name;

                            elemenLi.appendChild(elemenA)
                            ul.appendChild(elemenLi)
                        })
                    }else if(indeks == 'outsource'){

                        Object.keys(data.data[indeks]).forEach(function(indeks2) {
                            var elemenLi = document.createElement("li");

                            elemenLi.className = "list-group-item";
                            elemenLi.id = indeks + (parseInt(indeks2) + 1);

                            var elemenA = document.createElement("a");
                            elemenA.textContent = data.data[indeks][indeks2].name;

                            elemenLi.appendChild(elemenA)
                            ul_outsource.appendChild(elemenLi)
                        })
                    }else{
                    if(document.getElementById(indeks) !== null){

                        document.getElementById(indeks).value = data.data[indeks].name
                    }

                    }
                });
                var judulElement = document.getElementById('judul');
                judulElement.textContent = data.paper[0].innovation_title;

                var lokasiElement = document.getElementById('inovasi_lokasi');
                lokasiElement.textContent = data.paper[0].inovasi_lokasi;
                //document.getElementById('inovasi_lokasi').innerHTML = data.paper[0].inovasi_lokasi;
                // var lokasiElement = document.getElementById('inovasi_lokasi');
                //     if (data.papers && data.paper[0] && data.paper[0].inovasi_lokasi) {
                //         lokasiElement.textContent = data.paper[0].inovasi_lokasi;
                //     } else {
                //         lokasiElement.textContent = "Data tidak tersedia";
                //     }


                var abstractElement = document.getElementById('abstrak');
                abstractElement.textContent = data.paper[0].abstract;

                var problemElement = document.getElementById('problem');
                problemElement.textContent = data.paper[0].problem;

                // var problem_impactElement = document.getElementById('problem_impact');
                // problem_impactElement.textContent = data.paper[0].problem_impact;

                var main_causeElement = document.getElementById('main_cause');
                main_causeElement.textContent = data.paper[0].main_cause;

                var solutionElement = document.getElementById('solution');
                solutionElement.textContent = data.paper[0].solution;

                // var outcomeElement = document.getElementById('outcome');
                // outcomeElement.textContent = data.paper[0].outcome;

                // var performanceElement = document.getElementById('performance');
                // performanceElement.textContent = data.paper[0].performance;

                fotoTim =  '{{route('query.getFile')}}' + '?directory=' + data.paper[0].proof_idea;
                fotoInovasi =  '{{route('query.getFile')}}' + '?directory=' + data.paper[0].innovation_photo;

                // Set the URL as the source for the iframe
                document.getElementById("idFotoTim").src = fotoTim;
                document.getElementById("idFotoInovasi").src = fotoInovasi;
            },
            error: function(error) {
                // Menampilkan pesan kesalahan jika terjadi kesalahan dalam permintaan Ajax
                console.log(error.responseJSON);
                alert(error.responseJSON.message);
            }
        });

    }

    // digunakan untuk menghapus detail member ketika modal ditutup
    function remove_detail(){
        document.getElementById('facilitator').value = ''
        document.getElementById('leader').value = ''

        var elemenMember = document.getElementById('member-card')
        if(elemenMember != null){
            elemenMember.remove()
        }
        var elemenOutsource = document.getElementById('outsource-card');
        if(elemenOutsource != null){
            elemenOutsource.remove()
        }
    }
    // menjalnkan fungsi ketika modal ditutup
    $('#detailTeamMember').on('hidden.bs.modal', function () {
        remove_detail()
    });

    function check_admin_approve(idTeam){
        statusSelectField = document.getElementById('status_by_admin')
        adminButton = document.getElementById('accAdminButton')

        data_event = check_if_accept(idTeam)

        if(statusSelectField.value != "-" && data_event.status != 'not active' && data_event.event_name != undefined){
            adminButton.removeAttribute("disabled");
        }else{
            adminButton.setAttribute("disabled", true);
        }
    }

    function approve_paper_fasil_modal(idPaper){
        // alert(idPapern)
        var form = document.getElementById('accFasilPaperForm');

        var url = `{{ route('paper.approvePaperFasil', ['id' => ':idPaper']) }}`;
        url = url.replace(':idPaper', idPaper);
        form.action = url;
    }
    $('#accFasilitator').on('hidden.bs.modal', function () {
        var form = document.getElementById('accFasilPaperForm');

        form.removeAttribute('action');
    });

    function approve_benefit_fasil_modal(idPaper){
        // alert(idPapern)
        var form = document.getElementById('accFasilBenefitForm');

        var url = `{{ route('paper.approveBenefitFasil', ['id' => ':idPaper']) }}`;
        url = url.replace(':idPaper', idPaper);
        form.action = url;
    }
    $('#accFasilitatorBnefit').on('hidden.bs.modal', function () {
        var form = document.getElementById('accFasilBenefitForm');

        form.removeAttribute('action');
    });

    function approve_benefit_gm_modal(idPaper){
        // alert(idPapern)
        var form = document.getElementById('accGmBenefitForm');

        var url = `{{ route('paper.approveBenefitGM', ['id' => ':idPaper']) }}`;
        url = url.replace(':idPaper', idPaper);
        form.action = url;
    }
    $('#accGM').on('hidden.bs.modal', function () {
        var form = document.getElementById('accGmBenefitForm');

        form.removeAttribute('action');
    });

    function approve_admin_modal(idPaper, idTeam){
        // alert(idPapern)
        var form = document.getElementById('accAdminForm');
        statusSelectField = document.getElementById('status_by_admin')

        var url = `{{ route('paper.approveadmin', ['id' => ':idPaper']) }}`;
        url = url.replace(':idPaper', idPaper);
        form.action = url;

        // check_admin_approve()
        statusSelectField.setAttribute('onchange', `check_admin_approve(${idTeam})`)
    }

    function upload_document_modal(idPaper){

        document.getElementById("paper_id_input").value = idPaper;

        var form = document.getElementById('uploadDocumentForm');

        var url = `{{ route('paper.uploadDocument') }}`;
        form.action = url;
        form.method = post;
    }
    function show_document_modal(idPaper){
        // var fileUrl;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: '{{ route('query.custom') }}',
            dataType: 'json',
            data: {
                table: "document_supportings",
                join: {
                        'papers':{
                            'papers.id': 'document_supportings.paper_id'
                        },
                    },
                where: {
                    "papers.id": idPaper
                },
                limit: 100,
                select:[
                        'document_supportings.id as id',
                        'file_name',
                        'path',
                    ]
            },
            // dataType: 'json',
            success: function(response) {
                $('#resultContainer').empty();
                var container = $('#resultContainer');
                response.forEach(function(item) {
                    container.append(deleteBtn);

                    // Mendapatkan URL file
                    var fileUrl = '{{ route('query.getFile') }}' + '?directory=' + item.path;

                    // Menangani gambar
                    if (item.file_name.toLowerCase().endsWith('.jpg') || item.file_name.toLowerCase().endsWith('.png') || item.file_name.toLowerCase().endsWith('.jpeg')) {
                        var imgElement = $('<img>').attr({
                            'class': "w-100",
                            'src': fileUrl,
                            'alt': item.file_name,
                        });
                        container.append(imgElement);

                    // Menangani PDF
                    } else if (item.file_name.toLowerCase().endsWith('.pdf')) {
                        var iframeElement = document.createElement('iframe');
                        iframeElement.src = fileUrl;
                        iframeElement.width = '100%';
                        iframeElement.height = '720px';
                        resultContainer.appendChild(iframeElement);

                    // Menangani video MP4
                    }  else if (item.file_name.toLowerCase().endsWith('.mp4')) {
                        var videoElement = document.createElement('video');
                        videoElement.src = fileUrl;
                        videoElement.className = 'w-100'; // Menggunakan kelas Bootstrap untuk lebar 100%
                        videoElement.controls = true;
                        videoElement.type = 'video/mp4'; // Menambahkan type="video/mp4"
                        resultContainer.appendChild(videoElement);

                    // Menangani video MKV atau format lain yang mungkin tidak didukung
                    } else if (item.file_name.toLowerCase().endsWith('.mkv') || item.file_name.toLowerCase().endsWith('.avi')) {
                        container.append('<p>Browser mungkin tidak mendukung format video ' + item.file_name + '. Silakan unduh file untuk memutar video.</p>');
                        var downloadLink = $('<a>')
                            .attr('href', fileUrl)
                            .attr('download', item.file_name)
                            .text('Download ' + item.file_name)
                            .addClass('btn btn-primary');
                        container.append(downloadLink);
                    }

                    // Form untuk menghapus file
                    var form = $('<form>', {
                        method: 'POST',
                        action: '{{ route('paper.deleteDocument') }}'
                    });

                    var deleteBtn = $('<button>')
                                    .text('Delete')
                                    .attr('class', 'btn btn-danger my-3')
                                    .attr('type', 'submit');

                    form.append($('<input>', {
                        type: 'hidden',
                        name: '_method',
                        value: 'DELETE'
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: 'id',
                        value: item.id
                    }));

                    form.append($('<input>', {
                        type: 'hidden',
                        name: '_token',
                        value: '{{ csrf_token() }}'
                    }));

                    form.append(deleteBtn);

                    // Append form ke container
                    container.append(form);
                    container.append('<hr>');
                });
            },


            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function check_if_accept(idTeam){
        registEventDiv = document.getElementById('registEvent')
        statusSelectField = document.getElementById('status_by_admin')

        var year_now = new Date().getFullYear();
        // alert(year_now)
        if(statusSelectField.value == 'accept'){
            data_team = get_single_data_from_ajax('teams', {'id': idTeam})
            data_event = get_single_data_from_ajax('events', {
                'company_code': data_team.company_code,
                'status': ['active']
            },3)
            // console.log(data_event);
            if(data_event.event_name == undefined){
                new_input = `
                    <div class="mb-3">
                        <label class="mb-1" for="id_eventID">Event - Year</label>
                        <select class="form-select" aria-label="Default select example"
                            name="event_id" id="id_eventID"
                            placeholder="Pilih year" readonly required>
                            <option value=""> - </option>
                        </select>
                    </div>
                `;
            }else{
                new_input = `
                    <div class="mb-3">
                        <label class="mb-1" for="id_eventID">Event - Year</label>
                        <select class="form-select" aria-label="Default select example"
                            name="event_id" id="id_eventID"
                            placeholder="Pilih year" readonly required>
                            <option value="${data_event.id}"> ${data_event.event_name} - ${data_event.year}</option>
                        </select>
                    </div>

                `;
            }


            // <div class="mb-3">
            //         <label class="mb-1" for="id_year">Year</label>
            //         <select class="form-select" aria-label="Default select example"
            //             name="year" id="id_year"
            //             placeholder="Pilih year" required>
            //             <option value="${year_now}"> ${year_now}</option>
            //             <option value="${year_now + 1}"> ${year_now + 1}</option>
            //             <option value="${year_now + 2}"> ${year_now + 2}</option>
            //             <option value="${year_now + 3}"> ${year_now + 3}</option>
            //         </select>
            //     </div>

            registEventDiv.insertAdjacentHTML('beforeend', new_input);
        }else{
            registEventDiv.innerHTML = ""
        }

        return data_event
    }

    $('#accAdmin').on('hidden.bs.modal', function () {
        var form = document.getElementById('accAdminForm');

        form.removeAttribute('action');

        document.getElementById('registEvent').innerHTML = ""
        document.getElementById('status_by_admin').value = '-'
    });

    function get_single_data_from_ajax(table, data_where, limit_page=1) {
        let result_data
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            async: false,
            type: 'GET',
            url: '{{ route('query.custom') }}',
            dataType: 'json',
            data: {
                table: `${table}`,
                where: data_where,
                limit: limit_page
            },
            success: function(response) {
                // console.log(response[0]);
                result_data = response[0]
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                result_data = []
            }
        })
        return result_data
    }

function get_comment(idPaper, writer) {
    $.ajax({
        url: '/api/comments/by-paper',
        type: 'GET',
        data: {
            paper_id: idPaper
        },
        success: function (data) {
            let commentList = document.getElementById('commentList');
            let commentTitle = document.getElementById('commentTitle');

            // Kosongkan konten sebelumnya
            commentList.innerHTML = "";

            if (data.length > 0) {
                // Buat elemen untuk setiap komentar
                data.forEach(comment => {
                    let commentItem = document.createElement('div');
                    commentItem.className = 'mb-3 p-2 rounded border bg-white';
                    commentItem.innerHTML = `
                        <strong>${comment.writer}</strong>
                        <p class="mb-0 text-muted">${comment.comment}</p>
                    `;
                    commentList.appendChild(commentItem);
                });
            } else {
                // Tampilkan pesan jika tidak ada komentar
                commentList.innerHTML = `
                    <div class="text-muted text-center">
                        <em>No comments found for this paper.</em>
                    </div>
                `;
            }

            commentTitle.innerHTML = `List Komentar`;
        },
        error: function (xhr) {
            console.error('Failed to fetch comments:', xhr);
            document.getElementById('commentList').innerHTML = `
                <div class="text-danger text-center">
                    <em>Failed to load comments.</em>
                </div>
            `;
        }
    });
}

$('#commentModal').on('hidden.bs.modal', function () {
    document.getElementById('commentList').innerHTML = "";
    document.getElementById('commentTitle').innerHTML = "";
});


$('#commentModal').on('hidden.bs.modal', function () {
    document.getElementById('comment').value = "";
    document.getElementById('commentTitle').innerHTML = "";
});


    $('#commentModal').on('hidden.bs.modal', function () {
        document.getElementById('comment').value = ""
        document.getElementById('commentTitle').innerHTML = ""
    });

    function get_data_modal_update(team_id){

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: '{{ route('query.custom') }}',
            dataType: 'json',
            data: {
                table: "teams",
                where: {
                    "teams.id": team_id
                },
                limit: 1,
                join: {
                        'papers':{
                            'papers.team_id': 'teams.id'
                        },
                        'categories':{
                            'categories.id': 'teams.category_id'
                        },
                        'themes':{
                            'themes.id': 'teams.theme_id'
                        },
                    },
                select:[
                        'innovation_title',
                        'team_name',
                        'category_name',
                        'theme_name',
                        'themes.id as theme_id',
                        'categories.id as category_id',
                        'status_inovasi'
                    ]
            },
            // dataType: 'json',
            success: function(response) {
                console.log(response)
                document.getElementById("inputInnovationTitle").value = response[0].innovation_title;
                document.getElementById("inputTeamName").value = response[0].team_name;

                var statusInovasiSelected = document.getElementById("inputStatusInovasi"); // Menggunakan ID yang benar
                for (var i = 0; i < statusInovasiSelected.options.length; i++) {
                    if (statusInovasiSelected.options[i].value == response[0].status_inovasi) {
                        statusInovasiSelected.options[i].selected = true; // Menandai opsi sebagai terpilih
                        break;
                    }
                }

                //selected category
                var categorySelected = document.getElementById("inputCategory");
                // Loop through options and set the selected attribute for the matching themeId
                for (var i = 0; i < categorySelected.options.length; i++) {
                    if (categorySelected.options[i].value == response[0].category_id) {
                        categorySelected.options[i].selected = true;
                        break;
                    }
                }

                //selected theme
                var themeSelected = document.getElementById("inputTheme");
                // Loop through options and set the selected attribute for the matching themeId
                for (var i = 0; i < themeSelected.options.length; i++) {
                    if (themeSelected.options[i].value == response[0].theme_id) {
                        themeSelected.options[i].selected = true;
                        break;
                    }
                }

            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        var form = document.getElementById('updateDataTeam');
        var url = `{{ route('paper.update', ['id' => ':team_id']) }}`;
        url = url.replace(':team_id', team_id);
        form.action = url;
    }

    function get_data_modal_history(team_id) {
        $.ajax({
            url: '{{route('query.custom')}}',
            type: 'GET',
            data: {
                table: 'histories',
                where: {
                    "team_id": team_id
                },
                limit: 1000
            },
            dataType: 'json',
            success: function(data) {

                var history = $('#history'); // Dapatkan elemen dengan ID "history"
                history.empty(); // Membersihkan elemen history sebelum menambahkan data baru

                data.forEach(function(item) {
                    var createdAt = item.created_at;
                    var date = new Date(createdAt);
                    var day = date.getDate();
                    var month = date.getMonth() + 1; // Perhatikan bahwa bulan dimulai dari 0, jadi tambahkan 1
                    var year = date.getFullYear();
                    var formattedDate = ('0' + day).slice(-2) + '/' + ('0' + month).slice(-2) + '/' + year;


                    var timelineItem = $('<div class="timeline-item">');

                    var marker = $('<div class="timeline-item-marker">');
                    marker.append('<div class="timeline-item-marker-text">' + formattedDate + '</div>');
                    marker.append('<div class="timeline-item-marker-indicator"><i data-feather="check"></i></div>');

                    var content = $('<div class="timeline-item-content">' + item.activity + '</div>');

                    timelineItem.append(marker);
                    timelineItem.append(content);

                    history.append(timelineItem);
                });

                // Jika Anda ingin memanggil Feather Icons, Anda perlu menginisialisasi mereka setelah menambahkan elemen baru
                feather.replace()
            },
            error: function() {
                // Handle kesalahan jika terjadi
                console.log(error.responseJSON);
                alert(error.responseJSON.message);
                // console.error('Terjadi kesalahan saat mengambil data.');
            }
        });
    }

    function change_url(id, elementid) {
        //link untuk update -> rollback
        var form = document.getElementById(elementid);
        var url = `{{ route('paper.rollback', ['id' => ':id']) }}`;
        url = url.replace(':id', id);
        form.action = url;
    }

    function change_url_step(id, elementid, stage){
        var form = document.getElementById(elementid);
        var url = `{{ route('paper.store.file.stages', ['id' => ':id', 'stage' => ':stage']) }}`;
        url = url.replace(':id', id);
        url = url.replace(':stage', stage);
        form.action = url;
    }

</script>
@endpush
