@extends('layouts.app')
@section('title', 'Role | Admin')
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

        .select2-dropdown {
            z-index: 1001;
            /* Sesuaikan dengan z-index modal atau nilai yang sesuai */
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
                            Data Admin
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
                <div class="mb-3">
                    @if (Auth::user()->role === 'Superadmin')
                        <div class="row">
                            <div class="col-md-4">
                                <select id="company-filter" class="form-select">
                                    <option value="">Semua Perusahaan</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->company_code }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                </div>
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
    var table = $('#datatable-innovator').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('management-system.role.admin.index') }}",
            data: function(d) {
                d.company_code = $('#company-filter').val();
            }
        },
        columns: [
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                title: 'No',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'name',
                name: 'name',
                title: 'Nama'
            },
            {
                data: 'company_name',
                name: 'company_name',
                title: 'Nama Perusahaan'
            },
            {
                data: 'position_title',
                name: 'position_title',
                title: 'Posisi'
            },
            {
                data: 'department_name',
                name: 'department_name',
                title: 'Job'
            },
            {
                data: 'job_level',
                name: 'job_level',
                title: 'Level'
            }
        ],
        // Hilangkan dom dan buttons
        order: [[0, 'asc']]
    });

    // Handle company filter change
    $('#company-filter').on('change', function() {
        table.ajax.reload();
    });
});
    </script>
@endpush
