@extends('layouts.app')
@section('title', 'Management System | Perusahaan')
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
    <style type="text/css">
        .active-link-nav {
            background-color: rgba(13, 110, 253, 0.75);
            /* Warna biru Bootstrap bg-primary dengan opacity 75% */
        }

        .active-link-nav a {
            color: white;
            /* Menjaga teks tetap terlihat dengan warna putih */
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
                            <div class="page-header-icon"><i data-feather="grid"></i></div>
                            MANAGEMENT SYSTEM
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="mb-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-lg-9">
                <div class="card card-header-actions mb-4">
                    <div class="card-header">
                        Tabel Perusahaan
                        <button class="btn btn-primary text-white btn-sm" type="button" data-bs-toggle="modal"
                            data-bs-target="#createCompany"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp; Tambah
                            Perusahaan</button>
                    </div>
                    <div class="card-body">
                        <table id="datatable-company" class="display">
                        </table>
                    </div>
                </div>
            </div>
            <!-- Sticky Nav-->
            @include('auth.admin.management_system.team.rightbar')
        </div>
    </div>
    <!-- Your HTML content above -->

    {{-- Modal for create category --}}
    <div class="modal fade" id="createCompany" tabindex="-1" role="dialog" aria-labelledby="createCompanyLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Form Perusahaan</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('management-system.team.company.store') }}" method="POST">
                    @csrf @method('POST')
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="inputKodePerusahaan" class="form-label text-muted">Kode Perusahaan</label>
                            <input type="text" name="company_code" id="inputKodePerusahaan"
                                class="form-control rounded-3 shadow-sm" placeholder="Isi Kode Perusahaan" required>
                        </div>
                        <div class="mb-4">
                            <label for="inputNamaPerusahaan" class="form-label text-muted">Nama Perusahaan</label>
                            <input type="text" name="company_name" id="inputNamaPerusahaan"
                                class="form-control rounded-3 shadow-sm" placeholder="Isi Nama Perusahaan" required>
                        </div>
                        <div class="mb-4">
                            <label for="inGroupPerusahaan" class="form-label text-muted">Group Perusahaan</label>
                            <select name="group" id="inGroupPerusahaan" class="form-select rounded-3 shadow-sm" required>
                                <option value="Semen">Semen</option>
                                <option value="Non Semen">Non Semen</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-1" type="submit"
                            style="font-weight: 600;">
                            <i data-feather="save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        feather.replace();
    </script>


    <!-- Bootstrap Modal for Update -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Update Perusahaan</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateFormCompany" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" class="form-control">
                        <div class="mb-4">
                            <label for="inKodePerusahaan" class="form-label text-muted">Kode Perusahaan</label>
                            <input type="text" name="company_code" id="inKodePerusahaan"
                                class="form-control rounded-3 shadow-sm" placeholder="Isi kode perusahaan" required>
                        </div>
                        <div class="mb-4">
                            <label for="inNamaPerusahaan" class="form-label text-muted">Nama Perusahaan</label>
                            <input type="text" name="company_name" id="inNamaPerusahaan"
                                class="form-control rounded-3 shadow-sm" placeholder="Isi nama perusahaan" required>
                        </div>
                        <div class="mb-4">
                            <label for="inGroupPerusahaan" class="form-label text-muted">Group Perusahaan</label>
                            <select name="group" id="inGroupPerusahaan" class="form-select rounded-3 shadow-sm"
                                required>
                                <option value="Semen">Semen</option>
                                <option value="Non Semen">Non Semen</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-1" type="submit"
                            style="font-weight: 600;">
                            <i data-feather="save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        feather.replace();
    </script>


    {{-- modal untuk delete company --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formDeleteCompany" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="deleteModalTitle">Konfirmasi Hapus Perusahaan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fa fa-exclamation-triangle text-warning" style="font-size: 40px;"></i>
                            <p class="mt-3">Apakah Anda yakin ingin menghapus perusahaan ini?</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" style="font-weight: 600;">Hapus Perusahaan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script>
        $(document).ready(function() {
            var dataTable = $('#datatable-company').DataTable({
                "processing": true,
                "serverSide": false, // Since data is fetched by Ajax, set to false
                "ajax": {
                    "url": '{{ route('query.custom') }}',
                    "type": "GET",
                    "dataType": "json",
                    "data": {
                        table: 'companies',
                        limit: 100,
                        // Include other parameters as needed
                    },
                    "dataSrc": "" // Empty string or null to indicate that the data is at the root level
                },
                "columns": [{
                        "data": null,
                        "title": "No",
                        "render": function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": "company_code",
                        "title": "Kode Perusahaan"
                    },
                    {
                        "data": "company_name",
                        "title": "Nama Perusahaan"
                    },
                    {
                        "data": null,
                        "title": "Action",
                        "render": function(data, type, row) {
                            return '<button class="btn btn-warning btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#updateModal" onclick="updateCompany(' +
                                row.id +
                                ')"><i class="fa fa-pencil" aria-hidden="true"></i></button> <button class="btn btn-danger btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteCompany(' +
                                row.id +
                                ')"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                        }
                    },
                ],
                "scrollY": true,
                "scrollX": false,
                "stateSave": true,
            });
        });

        function updateCompany(companyId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',

                url: '{{ route('query.custom') }}',
                data: {
                    table: "companies",
                    where: {
                        "id": companyId
                    },
                    limit: 1
                },
                // dataType: 'json',
                success: function(response) {
                    console.log(response)
                    document.getElementById("id").value = response[0].id;
                    document.getElementById("inKodePerusahaan").value = response[0].company_code;
                    document.getElementById("inNamaPerusahaan").value = response[0].company_name;
                    if (response[0].group == 'Semen')
                        document.getElementById("inGroupPerusahaan").selectedIndex = 0;
                    else if (response[0].group == 'Non Semen')
                        document.getElementById("inGroupPerusahaan").selectedIndex = 1;
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

            //link untuk update
            var form = document.getElementById('updateFormCompany');
            var url = `{{ route('management-system.team.company.update', ['id' => ':companyId']) }}`;
            url = url.replace(':companyId', companyId);
            form.action = url;

        }

        function deleteCompany(companyId) {
            // Mengatur ID data yang akan dihapus dalam variabel JavaScript
            var form = document.getElementById('formDeleteCompany');
            var url = `{{ route('management-system.team.company.delete', ['id' => ':companyId']) }}`;
            url = url.replace(':companyId', companyId);
            form.action = url;
        }
    </script>
@endpush
