@extends('layouts.app')
@section('title', 'Management System | Tema')
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
                        Tabel Tema
                        <button class="btn btn-primary text-white btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#createTheme"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp; Tambah Tema</button>
                    </div>
                    <div class="card-body">
                        <table id="datatable-category" class="display">
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
<div class="modal fade" id="createTheme" tabindex="-1" role="dialog" aria-labelledby="createThemeLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createThemeLabel">Form Tema</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('management-system.team.theme.store')}}" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="mb-3">
                        <h6 for="inputNamaTema" class="small mb-1">Nama Tema</h6>
                        <input type="text" name="theme_name" value="" id="inputNamaTema" class="form-control" placeholder="Isi nama Tema" required>
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
                <h5 class="modal-title" id="uploadDocumentTitle">Update Tema</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateFormTheme" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Update form fields go here -->
                    <input type="hidden" name="id" value="" id="id" class="form-control">
                    <div class="mb-3">
                        <h6 for="inNamaTema" class="small mb-1">Nama Tema</h6>
                        <input type="text" name="theme_name" value="" id="inNamaTema" class="form-control" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" data-bs-dismiss="modal">Simpan</button>
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- modal untuk update category --}}
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formDeleteTheme" method="POST">
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
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Tutup</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
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
        var dataTable = $('#datatable-category').DataTable({
            "processing": true,
            "serverSide": false, // Since data is fetched by Ajax, set to false
            "ajax": {
                "url": '{{ route('query.custom') }}',
                "type": "GET",
                "dataType": "json",
                "data": {
                    table: 'themes',
                    limit: 100,
                    // Include other parameters as needed
                },
                "dataSrc": "" // Empty string or null to indicate that the data is at the root level
            },
            "columns": [
                {
                    "data": null,
                    "title": "No",
                    "render": function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {"data": "theme_name", "title": "Nama Tema"},
                {
                    "data": null,
                    "title": "Action",
                    "render": function (data, type, row) {
                        return '<button class="btn btn-warning btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#updateModal" onclick="updateCategory(' + row.id + ')"><i class="fa fa-pencil" aria-hidden="true"></i></button> <button class="btn btn-danger btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="deleteCategory(' + row.id + ')"><i class="fa fa-trash" aria-hidden="true"></i></button>';
                    }
                },
            ],
            "scrollY": true,
            "scrollX": false,
            "stateSave": true,
        });
    });

    function updateCategory(themeId) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',

            url: '{{ route('query.custom') }}',
            data: {
                table: "themes",
                where: {
                    "id": themeId
                },
                limit: 1
            },
            // dataType: 'json',
            success: function(response) {
                console.log(response)
                document.getElementById("id").value = response[0].id;
                document.getElementById("inNamaTema").value = response[0].theme_name;
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        //link untuk update
        var form = document.getElementById('updateFormTheme');
        var url = `{{ route('management-system.team.theme.update', ['id' => ':themeId']) }}`;
        url = url.replace(':themeId', themeId);
        form.action = url;

    }
    function deleteCategory(themeId) {
    // Mengatur ID data yang akan dihapus dalam variabel JavaScript
        var form = document.getElementById('formDeleteTheme');
        var url = `{{ route('management-system.team.theme.delete', ['id' => ':themeId']) }}`;
        url = url.replace(':themeId', themeId);
        form.action = url;
    }
</script>
@endpush
