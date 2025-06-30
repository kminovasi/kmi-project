@extends('layouts.app')

@section('title', 'Data Makalah')

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

        .editable-field {
            background-color: #fff;
            border: 2px solid #ced4da;
        }

        .editable-field[readonly] {
            background-color: #f8f9fa;
            border: 1px solid transparent;
        }

        .select2-container .select2-selection--single {
            height: 45px !important; /* Sesuaikan dengan kebutuhan */
            padding-top: .6rem;
        }

        /* Pastikan dropdown muncul di atas modal */
        .select2-container {
        z-index: 9999;
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
                            Data Makalah Inovasi
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="container-xl px-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Terjadi Kesalahan</strong> List Error:
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
                                <form id="modal-card-form" method="POST" action="">
                                    @csrf
                                    @method('PUT')
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="m-0">Detail Tim</h5>
                                        <button type="button" class="btn btn-sm btn-primary btn-edit-team-member">Edit Anggota Tim</button>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label" for="team-name">Nama Tim</label>
                                            <input class="form-control form-control-lg detail-team-input editable-field" name="team_name" id="team-name" type="text"
                                                value="" disabled />
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="facilitator">Fasilitator</label>
                                            <select class="form-control form-control-lg detail-team-input editable-field" name="facilitator" id="facilitator" disabled></select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="leader">Ketua</label>
                                            <select class="form-control form-control-lg detail-team-input editable-field" name="leader" id="leader" disabled> </select>
                                        </div>
                                    </div>
                                </form>
                            </div>


                            <form action="" id="detail-team-photo" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="m-0">Foto Tim</h5>
                                    </div>
                                    <div class="card-body text-center">
                                        <img src="" id="idFotoTim" alt="Foto Tim"
                                            class="img-fluid rounded-3 shadow-sm" />
                                    </div>
                                    <div class="mb-3 d-none px-2" id="inputTeamPhotoContainer">
                                        <label for="" class="form-label"></label>
                                        <input type="file" class="form-control" id="inputFotoTim"
                                            name="team_photo" accept=".jpg, .jpeg, .png" />
                                        <small class="text-muted">File png,jpg,jpeg Maks 5MB.</small>
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
                                    <div class="mb-3 d-none px-2" id="inputInnovationPhotoContainer">
                                        <label for="" class="form-label"></label>
                                        <input type="file" class="form-control" id="inputFotoInovasi"
                                            name="innovation_photo" accept=".jpg, .jpeg, .png" />
                                        <small class="text-muted">File png,jpg,jpeg Maks 5MB.</small>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-primary btn-edit-photo">Edit Gambar</button>
                            </form>

                        </div>
                        <div class="col-md-8">
                            <div class="card shadow-sm mb-3">
                                <form action="" id="detail-paper-form" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="m-0">Detail Makalah</h5>
                                        <button type="button" class="btn btn-sm btn-primary btn-edit-detail-paper">Edit Detail Paper</button>
                                    </div>
                                    <div class="card-body">
                                        <!-- Judul -->
                                        <div class="mb-3">
                                            <div class="fw-bold">Judul</div>
                                            <textarea class="form-control editable-field input-detail-paper" id="judul" name="innovation_title" rows="3" readonly></textarea>
                                        </div>
                                        <hr>
                                        <!-- Lokasi Implementasi Inovasi -->
                                        <div class="mb-3">
                                            <div class="fw-bold">Lokasi Implementasi Inovasi</div>
                                            <input class="form-control editable-field input-detail-paper" id="inovasi_lokasi" name="innovation_location" readonly/>
                                        </div>
                                        <hr>
                                        <!-- Abstrak -->
                                        <div class="mb-3">
                                            <div class="fw-bold">Abstrak</div>
                                            <textarea class="form-control editable-field input-detail-paper" id="abstrak" name="abstract" rows="6" readonly></textarea>
                                        </div>
                                        <hr>
                                        <!-- Permasalahan -->
                                        <div class="mb-3">
                                            <div class="fw-bold">Permasalahan</div>
                                            <textarea class="form-control editable-field input-detail-paper" id="problem" name="problem" rows="6" readonly></textarea>
                                        </div>
                                        <hr>
                                        <!-- Penyebab Utama -->
                                        <div class="mb-3">
                                            <div class="fw-bold">Penyebab Utama</div>
                                            <textarea class="form-control editable-field input-detail-paper" id="main_cause" name="main_cause" rows="6" readonly></textarea>
                                        </div>
                                        <hr>
                                        <!-- Solusi -->
                                        <div class="mb-3">
                                            <div class="fw-bold">Solusi</div>
                                            <textarea class="form-control editable-field input-detail-paper" id="solution" name="solution" rows="6" readonly></textarea>
                                        </div>
                                        <hr>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal untuk filter khusus superadmin --}}
    {{-- modal untuk filter khusus superadmin --}}
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content shadow">
                <!-- Header -->
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="Filter">Filter</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Body -->
                <div class="modal-body">
                    <form id="filterForm">
                        @if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin')
                            <input type="hidden" name="filter-role" id="filter-role" value="admin">
                        @endif

                        <!-- Company Filter -->
                        <div class="mb-3">
                            <label for="filter-company" class="form-label fw-semibold">Perusahaan</label>
                            <select id="filter-company" name="filter-company" class="form-select">
                                <option value="" selected>-- Pilih Perusahaan --</option>
                                @foreach ($data_company as $company)
                                    <option value="{{ $company->company_code }}">
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

                        <!-- Filter by Category -->
                        <div class="mb-3">
                            <label for="filter_category" class="form-label fw-semibold">Kategori</label>
                            <select id="filter_category" name="filter_category" class="form-select">
                                <option value="" selected>-- Pilih Kategori --</option>
                                @foreach ($data_category as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </form> <!-- ✅ Penutup form di tempat yang benar -->
                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit" form="filterForm">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>
    
    <x-paper.approve-fasil-modal />

    <x-paper.approve-benefit-modal-by-fasil />
    <x-paper.approve-benefit-by-general-manager />
    <x-paper.approve-admin-modal />

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
                            <small class="text-muted">Maksimal 100MB per file.</small>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="modal-footer d-flex justify-content-end">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Tutup
                        </button>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-upload"></i> Kirim
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
                    <h5 class="modal-title fw-bold text-white" id="showDocumentTitle">Dokumen Pendukung</h5>
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

    <x-paper.comment-modal />

    {{-- modal untuk info update --}}
    <div class="modal fade" id="updateData" tabindex="-1" role="dialog" aria-labelledby="updateDataTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
            <div class="modal-content shadow-sm border-0">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="updateDataTitle">Perbarui Data</h5>
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
                            <label for="inputInnovationTitle" class="form-label fw-semibold">Judul Inovasi</label>
                            <input type="text" class="form-control shadow-sm" id="inputInnovationTitle"
                                name="innovation_title" value="" placeholder="Enter Innovation Title">
                        </div>

                        <!-- Team Name -->
                        <div class="mb-3">
                            <label for="inputTeamName" class="form-label fw-semibold">Nama Tim</label>
                            <input type="text" class="form-control shadow-sm" id="inputTeamName" name="team_name"
                                value="" placeholder="Enter Team Name">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label for="inputCategory" class="form-label fw-semibold">Kategori</label>
                            <select class="form-select shadow-sm" id="inputCategory" name="category">
                                @foreach ($data_category as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Theme -->
                        <div class="mb-3">
                            <label for="inputTheme" class="form-label fw-semibold">Tema</label>
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
                        <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary px-4">Kirim</button>
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
                    <h5 class="modal-title fw-bold text-white" id="infoHistoryTitle">Riwayat Aktivitas</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <!-- Body -->
                <div class="modal-body">
                    <div class="timeline" id="history">
                        <!-- Example timeline item -->
                        <h4>Header</h4>
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
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
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
                            <label for="rollback_option" class="form-label fw-semibold">Pilih Rollback</label>
                            <select class="form-select shadow-sm" name="rollback_option" id="rollback_option">
                                <option value="full_paper">Rollback Paper</option>
                                <option value="benefit">Rollback Benefit</option>
                            </select>
                        </div>
                        <!-- Comment -->
                        <div class="mb-4">
                            <label for="inputCommentRollback" class="form-label fw-semibold">Komentar</label>
                            <textarea name="comment" id="inputCommentRollback" rows="5" class="form-control shadow-sm"
                                placeholder="Add your comments here..."></textarea>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary" type="submit">RollBack</button>
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
                    <h5 class="modal-title fw-bold text-white" id="uploadDocumentTitle">Unggah Dokumen</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="uploadStepForm" class="upload-step-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="hidden" name="paper_id" id="paper_id_input" value="">
                            <input type="file" name="file_stage" class="form-control" multiple accept=".pdf">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary btn-upload-step" type="submit"
                            data-bs-dismiss="modal">Kirim</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-paper.paper-confirmation-modal />


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
                data: function (d) {
                     d.filterCompany = $('#filter-company').val();
                     d.filterRole = $('#filter-role').val();
                     d.status_inovasi = $('#filter-status-inovasi').val(); 
                     d.filter_category = $('#filter_category').val(); //ambil nilai yg dipilih ke server
                     return d;
                }
            },
            "columns": [{
                    "data": "DT_RowIndex",
                    "title": "No"
                }, {
                    "data": "innovation_title",
                    "title": "Judul Inovasi"
                }, {
                    "data": "team_name",
                    "title": "Nama Tim"
                }, {
                    "data": "detail_team",
                    "title": "Detail Tim"
                }, {
                    "data": "company_name",
                    "title": "Perusahaan"
                }, {
                    "data": "category_name",
                    "title": "Kategori"
                }, {
                    "data": "theme_name",
                    "title": "Tema"
                },
                {
                    "data": "metodologi_makalah",
                    "title": "Metodologi Makalah"
                },
                {
                    "data": "step_1",
                    "title": "Langkah 1"
                }, {
                    "data": "step_2",
                    "title": "Langkah 2"
                }, {
                    "data": "step_3",
                    "title": "Langkah 3"
                }, {
                    "data": "step_4",
                    "title": "Langkah 4"
                }, {
                    "data": "step_5",
                    "title": "Langkah 5"
                }, {
                    "data": "step_6",
                    "title": "Langkah 6"
                }, {
                    "data": "step_7",
                    "title": "Langkah 7"
                }, {
                    "data": "step_8",
                    "title": "Langkah 8"
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
                },
            ],
            "scrollX": true,
            "scrollY": true,
            "stateSave": true,
            "fixedHeader": {
                header: true,
                headerOffset: 60
            }
        });
    });
</script>


@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script type="">
    $(document).ready(function() {
       $('#accFasilitator').on('hidden.bs.modal', function() {
            const form = $('#accFasilPaperForm');
            form.removeAttr('action'); // Hapus atribut action dari form
            $(this).find('#stepsContainer').remove(); // Cari dan hapus stepsContainer dalam modal
                $('#status_by_fasil').val('-'); // Set kembali nilai default dropdown
            currentPaperId = null; // Reset currentPaperId
        });

        var dataTable = $('#datatable-makalah').DataTable({
            "processing": true,
            "serverSide": true,
            // "responsive" : true,
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
                     d.status_inovasi = $('#filter-status-inovasi').val(); 
                     d.filter_category = $('#filter_category').val(); //ambil nilai yg dipilih ke server
                     return d;
                }
            },
            "columns": [
                {"data": "DT_RowIndex", "title": "No"},
                {"data": "innovation_title", "title": "Judul Inovasi"},
                // {"data": "inovasi_lokasi", "tittle": "Lokasi Inovasi"},
                {"data": "team_name", "title": "Nama Tim"},
                {"data": "detail_team","title": "Detail Tim"},
                {"data": "company_name","title": "Perusahaan"},
                {"data": "category_name","title": "Kategori"},
                {"data": "theme_name","title": "Tema"},
                {"data": "metodologi_makalah","title": "Metodologi Makalah"},
                {"data": "step_1","title": "Langkah 1"},
                {"data": "step_2","title": "Langkah 2"},
                {"data": "step_3","title": "Langkah 3"},
                {"data": "step_4","title": "Langkah 4"},
                {"data": "step_5","title": "Langkah 5"},
                {"data": "step_6","title": "Langkah 6"},
                {"data": "step_7","title": "Langkah 7"},
                {"data": "step_8","title": "Langkah 8"},
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
            "fixedHeader": {
                header: true,
                headerOffset: 60
            }
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
        
        $('#filter-company').on('change', function () {
            dataTable.ajax.reload();
        });
        
        $('#filter_category').on('change', function () {
            dataTable.ajax.reload();
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
                // console.log(data);

                if(typeof data.data.member){
                    new_div_member = `
                    <div class="mb-3" id="member-card">
                        <label class="mb-1 ms-3" for="dataName">Team Member</label>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="List">
                            </ul>
                        </div>
                    </div>`;

                    document.getElementById('modal-card-form').insertAdjacentHTML('beforeend', new_div_member);
                    var ul = document.getElementById('List')
                }

                if(typeof data.data.outsource){
                    new_div_outsource = `
                    <div class="mb-3" id="outsource-card">
                        <label class="mb-1 ms-3" for="dataName">Team Member Outsource</label>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush" id="outsource-List">

                            </ul>
                        </div>
                    </div>
                    `;
                    document.getElementById('modal-card-form').insertAdjacentHTML('beforeend', new_div_outsource);
                    var ul_outsource = document.getElementById('outsource-List')
                }

                function buatElemenList(id, isSelect, value = "", text = "", isDisabled = true) {
                    let li = document.createElement("li");
                    li.className = "list-group-item d-flex align-items-center gap-2";
                    li.id = id;

                    let input;
                    if (isSelect) {
                        input = document.createElement("select");
                        input.className = "form-control detail-team-input editable-field select2";
                        input.name = "member[]";
                        input.id = id + "_select";

                        let option = document.createElement("option");
                        option.value = value;
                        option.text = text;
                        input.appendChild(option);

                        if (isDisabled) input.disabled = true;
                    } else {
                        input = document.createElement("input");
                        input.className = "form-control detail-team-input editable-field";
                        input.name = "outsource[]";
                        input.placeholder = "Masukkan nama anggota outsource";
                        input.value = value;
                        input.disabled = isDisabled;
                    }

                    let btnDelete = document.createElement("button");
                    btnDelete.type = "button";
                    btnDelete.className = `btn btn-sm btn-danger ${isDisabled ? 'd-none' : ''} ${isSelect ? 'btn-delete-member' : 'btn-delete-outsource'}`;
                    btnDelete.textContent = "Hapus";
                    btnDelete.onclick = () => li.remove();

                    li.appendChild(input);
                    li.appendChild(btnDelete);

                    return { li, input };
                }

                Object.keys(data.data).forEach(function(indeks) {
                    if (indeks === 'member') {
                        data.data[indeks].forEach((item, i) => {
                            let id = `member${i + 1}`;
                            let { li, input } = buatElemenList(id, true, item.employee_id, item.name);
                            ul.appendChild(li);
                            search_select2(input.id);
                        });

                        // Tombol Tambah Member
                        let tambahBtn = document.createElement("button");
                        tambahBtn.type = "button";
                        tambahBtn.className = "btn btn-sm btn-primary ms-3 mt-2 d-none";
                        tambahBtn.id = "add-member-btn";
                        tambahBtn.textContent = "Tambah Anggota";
                        tambahBtn.onclick = function () {
                            let newIndex = ul.children.length + 1;
                            let id = `member${newIndex}`;
                            let { li, input } = buatElemenList(id, true, "", "", false);
                            ul.appendChild(li);
                            search_select2(input.id);
                        };
                        ul.parentNode.appendChild(tambahBtn);
                    } else if (indeks === 'outsource') {
                        data.data[indeks].forEach((item, i) => {
                            let id = `outsource${i + 1}`;
                            let { li } = buatElemenList(id, false, item.name);
                            ul_outsource.appendChild(li);
                        });
                    } else {
                        if (document.getElementById(indeks)) {
                            document.getElementById(indeks).value = data.data[indeks].name;
                        }
                        if (!document.getElementById('add-member-btn')) {
                            let tambahBtn = document.createElement("button");
                            tambahBtn.type = "button";
                            tambahBtn.className = "btn btn-sm btn-primary ms-3 mt-2 d-none";
                            tambahBtn.id = "add-member-btn";
                            tambahBtn.textContent = "Tambah Anggota";
                            tambahBtn.onclick = function () {
                                let newIndex = ul.children.length + 1;
                        
                                let li = document.createElement("li");
                                li.className = "list-group-item d-flex align-items-center gap-2";
                                li.id = 'member' + newIndex;
                        
                                let select = document.createElement("select");
                                select.className = "form-control detail-team-input editable-field select2";
                                select.id = 'member' + newIndex + "_select";
                                select.name = "member[]";
                        
                                let btnDelete = document.createElement("button");
                                btnDelete.type = "button";
                                btnDelete.className = "btn btn-sm btn-danger btn-delete-member";
                                btnDelete.innerText = "Hapus";
                                btnDelete.onclick = function () {
                                    li.remove();
                                };
                        
                                li.appendChild(select);
                                li.appendChild(btnDelete);
                                ul.appendChild(li);
                        
                                search_select2(select.id);
                            };
                            ul.parentNode.appendChild(tambahBtn);
                        }
                    }
                });

                // Tombol Tambah Outsource (dipasang 1x saja di luar blok if-else)
                let tambahOutsourceBtn = document.createElement("button");
                tambahOutsourceBtn.type = "button";
                tambahOutsourceBtn.className = "btn btn-sm btn-primary ms-3 mt-2 d-none";
                tambahOutsourceBtn.id = "add-outsource-btn";
                tambahOutsourceBtn.textContent = "Tambah Outsource";
                tambahOutsourceBtn.onclick = function () {
                    let newIndex = ul_outsource.children.length + 1;
                    let id = `outsource${newIndex}`;
                    let { li } = buatElemenList(id, false, "", "", false);
                    ul_outsource.appendChild(li);
                };
                ul_outsource.parentNode.appendChild(tambahOutsourceBtn);


                // Set action form
                document.getElementById('modal-card-form').setAttribute('action', 'paper/update-detail-team/' + data.paper[0].team_id);
                document.getElementById('detail-paper-form').setAttribute('action', 'paper/update-detail-paper/' + data.paper[0].paper_id);
                document.getElementById('detail-team-photo').setAttribute('action', 'paper/update-detail-photo/' + data.paper[0].paper_id);
                
                const toggleElements = [
                    '.btn-edit-photo',
                    '.btn-edit-detail-paper',
                    '.btn-edit-team-member'
                ];
                
                toggleElements.forEach(selector => {
                    const el = document.querySelector(selector);
                    if (el) {
                        el.classList.toggle('d-none', !data.isEventActive);
                    }
                });

                // Set simple field values
                const fields = {
                    'team-name': data.paper[0].team_name,
                    'judul': data.paper[0].innovation_title,
                    'inovasi_lokasi': data.paper[0].inovasi_lokasi,
                    'abstrak': data.paper[0].abstract,
                    'problem': data.paper[0].problem,
                    'main_cause': data.paper[0].main_cause,
                    'solution': data.paper[0].solution
                };

                for (let id in fields) {
                    let el = document.getElementById(id);
                    if (el) el.value = fields[id];
                }

                // Set facilitator option
                let facilitator = document.getElementById('facilitator');
                if (facilitator && data.data.facilitator) {
                    facilitator.innerHTML = '';
                    let opt = new Option(data.data.facilitator.name, data.data.facilitator.employee_id);
                    facilitator.appendChild(opt);
                }

                // Set leader option
                let leader = document.getElementById('leader');
                if (leader && data.data.leader) {
                    leader.innerHTML = '';
                    let opt = new Option(data.data.leader.name, data.data.leader.employee_id);
                    leader.appendChild(opt);
                }

                // Set iframe source for foto tim dan inovasi
                const routeGetFile = '{{ route('query.getFile') }}';
                document.getElementById('idFotoTim').src = `${routeGetFile}?directory=${encodeURIComponent(data.paper[0].proof_idea)}`;
                document.getElementById('idFotoInovasi').src = `${routeGetFile}?directory=${encodeURIComponent(data.paper[0].innovation_photo)}`;
            },
            error: function(error) {
                alert(error.responseJSON.message);
            }
        });

    }

    // Fungsi untuk mencari fasilitator menggunakan Select2
    function search_facilitator(select_element_id) {
            $('#' + select_element_id).select2({
                dropdownParent: $('#detailTeamMember'),
                allowClear: true,
                width: "100%",
                placeholder: "Pilih Fasilitator",
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET', // Metode HTTP POST
                    url: '{{ route('query.get_fasilitator') }}',
                    dataType: 'json',
                    delay: 250, // Penundaan dalam milidetik sebelum permintaan AJAX dikirim
                    data: function(params) {
                        // Data yang akan dikirim dalam permintaan POST
                        return {
                            query: params.term // Menggunakan nilai input "query" sebagai parameter
                        };
                    },
                    processResults: function(data) {
                        // Memformat data yang diterima untuk format yang sesuai dengan Select2
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name, // Nama yang akan ditampilkan di kotak seleksi
                                    id: item.employee_id // Nilai yang akan dikirimkan saat opsi dipilih
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }
    
    // Fungsi untuk mencari anggota tim menggunakan Select2
    function search_select2(select_element_id) {
            $('#' + select_element_id).select2({
                allowClear: true,
                dropdownParent: $('#detailTeamMember'),
                width: "100%",
                placeholder: "Pilih " + select_element_id.split("_")[1] + (select_element_id.split("_")[2] ? " " +
                    select_element_id.split("_")[2] + " : " : " : "),
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST', // Metode HTTP POST
                    url: '{{ route('query.autocomplete') }}',
                    dataType: 'json',
                    delay: 250, // Penundaan dalam milidetik sebelum permintaan AJAX dikirim
                    data: function(params) {
                        // Data yang akan dikirim dalam permintaan POST
                        return {
                            query: params.term // Menggunakan nilai input "query" sebagai parameter
                        };
                    },
                    processResults: function(data) {
                        // Memformat data yang diterima untuk format yang sesuai dengan Select2
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.name, // Nama yang akan ditampilkan di kotak seleksi
                                    id: item.employee_id // Nilai yang akan dikirimkan saat opsi dipilih
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }
    
    document.addEventListener("DOMContentLoaded", function() {
        search_facilitator('facilitator');
        search_select2('leader');
    });

    document.querySelector('.btn-edit-team-member').addEventListener('click', function () {
        let inputs = document.querySelectorAll('.detail-team-input');
        let sedangEdit = false;

        inputs.forEach(function (input) {
            if (input.disabled) {
                sedangEdit = true;
            }
        });

        if (sedangEdit) {
            // Aktifkan mode edit
            inputs.forEach(function (input) {
                input.disabled = false;
            });

            // Tampilkan tombol tambah
            document.querySelector('#add-member-btn')?.classList.remove('d-none');
            document.querySelector('#add-outsource-btn')?.classList.remove('d-none');

            // Tampilkan semua tombol hapus yang tersembunyi
            document.querySelectorAll('.btn-delete-member, .btn-delete-outsource').forEach(function (btn) {
                btn.classList.remove('d-none');
            });

            this.textContent = 'Simpan Perubahan';
        } else {
            // Submit form
            document.querySelector('#modal-card-form').submit();
        }
    });

    document.querySelector('.btn-edit-detail-paper').addEventListener('click', function() {
        let inputs = document.querySelectorAll('.input-detail-paper');
        inputs.forEach(function(input) {
            if(input.readOnly){
                input.readOnly = false;
                document.querySelector('.btn-edit-detail-paper').textContent = 'Simpan Perubahan';
            } else {
                document.querySelector('#detail-paper-form').submit();
            }
        });
    });

    document.querySelector('.btn-edit-photo').addEventListener('click', function () {
        const inputTeam = document.querySelector('#inputTeamPhotoContainer');
        const inputInnovation = document.querySelector('#inputInnovationPhotoContainer');
        const btn = document.querySelector('.btn-edit-photo');

        // Jika sedang dalam mode edit (hidden)
        if (inputTeam.classList.contains('d-none') || inputInnovation.classList.contains('d-none')) {
            inputTeam.classList.remove('d-none');
            inputInnovation.classList.remove('d-none');
            btn.textContent = 'Simpan Perubahan';
        } else {
            document.querySelector('#detail-team-photo').submit();
        }
    });

    // digunakan untuk menghapus detail member ketika modal ditutup
    function remove_detail() {
        // Helper: reset dan set disabled/readOnly
        function resetField(id, option = 'disabled') {
            let el = document.getElementById(id);
            if (el) {
                el.value = '';
                el.innerHTML = '';
                el[option] = true;
            }
        }

        // Reset field dengan "disabled"
        ['team-name', 'facilitator', 'leader'].forEach(id => resetField(id, 'disabled'));

        // Reset field dengan "readOnly"
        ['judul', 'inovasi_lokasi', 'abstrak', 'problem', 'main_cause', 'solution'].forEach(id => resetField(id, 'readOnly'));

        // Hapus elemen member & outsource jika ada
        ['member-card', 'outsource-card'].forEach(id => {
            let el = document.getElementById(id);
            if (el) el.remove();
        });

        ['inputTeamPhotoContainer', 'inputInnovationPhotoContainer'].forEach(id => {
            let el = document.getElementById(id);
            if (el) el.classList.add('d-none');
        });

        // Ubah teks tombol
        const btnEditTeam = document.querySelector('.btn-edit-team-member');
        if (btnEditTeam) btnEditTeam.textContent = 'Edit Anggota Tim';

        const btnEditPaper = document.querySelector('.btn-edit-detail-paper');
        if (btnEditPaper) btnEditPaper.textContent = 'Edit Detail Paper';

        const btnEditPhoto = document.querySelector('.btn-edit-photo');
        if (btnEditPhoto) btnEditPhoto.textContent = 'Edit Foto';
    }

    // menjalnkan fungsi ketika modal ditutup
    const detailModal = document.getElementById('detailTeamMember');
        if (detailModal) {
            detailModal.addEventListener('hidden.bs.modal', function () {
                remove_detail();
            });
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
            url: '/paper/view-supporting-document/' + idPaper, // Gunakan route langsung
            dataType: 'json',
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
                // console.error(xhr.responseText);
            }
        });
    }

    function remove_document_modal() {
        $('#resultContainer').empty();
    }
    
    $('#showDocument').on('hidden.bs.modal', function () {
        remove_document_modal();
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
                // console.error(xhr.responseText);
                result_data = []
            }
        })
        return result_data
    }

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
                // console.log(response)
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
                // console.error(xhr.responseText);
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

                const history = $('#history'); // Dapatkan elemen dengan ID "history"
                history.empty(); // Membersihkan elemen history sebelum menambahkan data baru
                
                data.forEach(function(item) {
                    let createdAt = item.created_at;
                    let date = new Date(createdAt);
                    let day = date.getDate();
                    let month = date.getMonth() + 1; // Perhatikan bahwa bulan dimulai dari 0, jadi tambahkan 1
                    let year = date.getFullYear();
                    let formattedDate = ('0' + day).slice(-2) + '/' + ('0' + month).slice(-2) + '/' + year;

                    const headerContent = '<div class="py-3"><h5>' + item.activity + '</h5></div>'
                    
                    if(item.activity === 'Accepted to Event Group' || item.activity === 'Accepted to Event Internal'){
                        let header = $(headerContent)
                        history.append(header);
                    } else {

                        let timelineItem = $('<div class="timeline-item">');

                        let marker = $('<div class="timeline-item-marker">');
                        marker.append('<div class="timeline-item-marker-text">' + formattedDate + '</div>');
                        marker.append('<div class="timeline-item-marker-indicator"><i data-feather="check"></i></div>');

                        let content = $('<div class="timeline-item-content">' + item.activity + '</div>');

                        timelineItem.append(marker);
                        timelineItem.append(content);

                        history.append(timelineItem);
                    }
                });

                // Jika Anda ingin memanggil Feather Icons, Anda perlu menginisialisasi mereka setelah menambahkan elemen baru
                feather.replace()
            },
            error: function() {
                alert(error.responseJSON.message);
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
