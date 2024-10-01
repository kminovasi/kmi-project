@extends('layouts.app')
@section('title', 'Management System | Perusahaan')
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<style type="text/css">
    .active-link {
        color: #ffc004;
        background-color: #e81500;
    }
    #stickyNav .nav-item {
        margin-bottom: 10px;
        font-size: 16px;
    }
    .active-link-nav{
        background-color: rgb(232, 21, 0, 0.5);
    }
    .active-link-nav a{
        color : white;
    }
    .display thead th,
    .display tbody td {
        border: 0.5px solid #ddd; /* Atur warna dan ketebalan garis sesuai kebutuhan */
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
                @if(session('success'))
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
                        <button class="btn btn-primary text-white btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#createCompany"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp; Tambah Perusahaan</button>
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
<div class="modal fade" id="createCompany" tabindex="-1" role="dialog" aria-labelledby="createCompanyLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCompanyLabel">Form Perusahaan</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('management-system.team.company.store')}}" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 for="inputKodePerusahaan" class="small mb-1">Kode Perusahaan</h6>
                        <input type="text" name="company_code" value="" id="inputKodePerusahaan" class="form-control" placeholder="Isi kode perusahaan" required>
                    </div>
                    <div class="mb-3">
                        <h6 for="inputNamaPerusahaan" class="small mb-1">Nama Perusahaan</h6>
                        <input type="text" name="company_name" value="" id="inputNamaPerusahaan" class="form-control" placeholder="Isi nama perusahaan" required>
                    </div>
                    <div class="mb-3">
                        <h6 for="inputGroupPerusahaan" class="small mb-1">Group Perusahaan</h6>
                        <select class="form-select" aria-label="Default select example" name="group" id="inGroupPerusahaan" required>
                            <option id="opsi_Semen" value="Semen">Semen</option>
                            <option id="opsi_Non_Semen" value="Non Semen">Non Semen</option>
                        </select>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
        
    </div>
</div>

<!-- Bootstrap Modal for Update -->
<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDocumentTitle">Update Perusahaan</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateFormCompany" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Update form fields go here -->
                    <input type="hidden" name="id" value="" id="id" class="form-control">
                    <div class="mb-3">
                        <h6 for="inKodePerusahaan" class="small mb-1">Kode Perusahaan</h6>
                        <input type="text" name="company_code" value="" id="inKodePerusahaan" class="form-control" placeholder="Isi kode perusahaan" required>
                    </div>
                    <div class="mb-3">
                        <h6 for="inNamaPerusahaan" class="small mb-1">Nama Perusahaan</h6>
                        <input type="text" name="company_name" value="" id="inNamaPerusahaan" class="form-control" placeholder="Isi nama perusahaan" required>
                    </div>
                    <div class="mb-3">
                        <h6 for="inGroupPerusahaan" class="small mb-1">Nama Perusahaan</h6>
                        <select class="form-select" aria-label="Default select example" name="group" id="inGroupPerusahaan" required>
                            <option id="opsi_Semen" value="Semen">Semen</option>
                            <option id="opsi_Non_Semen" value="Non Semen">Non Semen</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" data-bs-dismiss="modal">Submit</button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- modal untuk update category --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formDeleteCompany" method="POST">
            @csrf
            @method ('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalTitle">Konfirmasi Hapus Data</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah yakin data ini akan dihapus ?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger" type="submit">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
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
            "columns": [
                {"data": "id", "title": "No"},
                {"data": "company_code", "title": "Kode Perusahaan"},
                {"data": "company_name", "title": "Nama Perusahaan"},
                {
                    "data": null,
                    "title": "Action",
                    "render": function (data, type, row) {
                        return '<button class="btn btn-warning btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#updateModal" onclick="updateCompany(' + row.id + ')"><i class="fa fa-pencil" aria-hidden="true"></i></button> <button class="btn btn-danger btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteCompany(' + row.id + ')"><i class="fa fa-trash" aria-hidden="true"></i></button>';
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
                if(response[0].group == 'Semen')
                    document.getElementById("inGroupPerusahaan").selectedIndex = 0;
                else if(response[0].group == 'Non Semen')
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
