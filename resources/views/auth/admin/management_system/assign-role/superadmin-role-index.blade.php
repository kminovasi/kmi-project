@extends('layouts.app')
@section('title', 'Role | Super Admin')
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
    <style type="text/css">
        .active-link {
            color: #ffc004;
            background-color: #e81500;
        }

        .display thead th,
        .display tbody td {
            border: 0.5px solid #ddd;
            /* Atur warna dan ketebalan garis sesuai kebutuhan */
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
                            Data Superadmin
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
            @if (session('success'))
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
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                    d.role = 'Superadmin'
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
