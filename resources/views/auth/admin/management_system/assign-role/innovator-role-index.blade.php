@extends('layouts.app')
@section('title', 'Role | Innovator')
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<style type="text/css">
    .active-link {
        color: #ffc004;
        background-color: #e81500;
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
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Data Innovator
                        </h1>
                    </div>
                     <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('management-system.role.index') }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
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
        <div class="card mb-4">
            <div class="card-body">
                {{-- <div class="mb-3">
                    @if (Auth::user()->role == 'Admin')
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
                    @endif
                </div> --}}
                <table id="datatable-innovator">
                    
                </table>
            </div>
            
        </div>
    </div>

@endsection

@push('js')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script type="">
$(document).ready(function() {
    var dataTable = $('#datatable-innovator').DataTable({
        "processing": true,
        "serverSide": false, // Since data is fetched by Ajax, set to false
        "ajax": {
            "url": '{{ route('query.get_role') }}',
            "type": "GET",
            "dataType": "json",
            "dataSrc": function (data) {
                // console.log('Jumlah data total: ' + data.recordsTotal);
                // console.log('Jumlah data setelah filter: ' + data.recordsFiltered);
                // console.log('Jumlah data setelah filter: ' + data.data);
                return data.data;
            },
            "data": function (d) {
                    d.role = 'User'
            },
            
        },
        "columns": [
            {"data": "DT_RowIndex", "title": "No"},
            {"data": "name", "title": "Name"},
            {"data": "co_name", "title": "Perusahaan"},
            {"data": "position_title", "title": "Posisi"},
            {"data": "job_level", "title": "Job Level"}
        ],
        "scrollY": true,
        "scrollX": false,
        "stateSave": true,
    });
});



</script>
@endpush
