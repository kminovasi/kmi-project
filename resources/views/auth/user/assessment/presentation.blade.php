@extends('layouts.app')
@section('title', 'Presentasi - Portal Inovasi')
@push('css')
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <style type="text/css">
        .active-link {
            color: #ffc004;
            background-color: #e81500;
        }

        .submit {
            width: 200px;
        }

        .next {
            width: 200px;
            margin-left: 10px;
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
                            Data Paper - Innovation Paper
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="p-2">
            <!-- <a href="{{ route('paper.index') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.index') ? 'active-link' : '' }}">Paper</a>
                                                                                                            <a href="{{ route('paper.register.team') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.register.team') ? 'active-link' : '' }}">Register</a>
                                                                                                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
    <a href="{{ route('assessment.presentation') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.presentation') ? 'active-link' : '' }}">Assessment</a> -->
            <!-- <a href="" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1">Event</a> -->
            <!-- <a href="{{ route('paper.event') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.event') ? 'active-link' : '' }}">Event</a>
@elseif(Auth::user()->role == 'Juri')
    <a href="{{ route('assessment.presentation') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.presentation') ? 'active-link' : '' }}">Assessment</a>
    @endif -->
            <a href="{{ route('paper.register.team') }}"
                class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.register.team') ? 'active-link' : '' }}">Register</a>

            <a href="{{ route('paper.index') }}"
                class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.index') ? 'active-link' : '' }}">Makalah
                Inovasi</a>

            @if (Auth::user()->role == 'Juri' ||
                    Auth::user()->role == 'BOD' ||
                    Auth::user()->role == 'Admin' ||
                    Auth::user()->role == 'Superadmin' ||
                    $is_judge)
                <a href="{{ route('assessment.on_desk') }}"
                    class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.on_desk') ? 'active-link' : '' }}">Assessment</a>
            @endif

            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
                <a href="{{ route('paper.event') }}"
                    class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.event') ? 'active-link' : '' }}">Event</a>
            @endif

        </div>
        <div class="mb-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('errors'))
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    {{ session('errors') }}

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        @include('auth.user.assessment.bar')
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    @if (Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin' || Auth::user()->role == 'Juri')
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                            data-bs-target="#filterModal">Filter</button>
                    @endif
                </div>
                <form id="datatable-card" action="{{ route('assessment.fix.pa') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <table id="datatable-competition" class="display"></table>
                    <hr>

                    <input type="text" class="form-control" name="category" id="category-pa" hidden>
                    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
                        <div class="d-flex">
                            <button type="submit" class="btn btn-primary next shadow-sm">Submit</button>
                            <button type="button" class="btn btn-outline-primary next shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#fixModalPA">Submit All</button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- modal untuk filter khusus admin dan juri --}}
    <div class="modal fade" id="filterModal" role="dialog" aria-labelledby="detailTeamMemberTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailTeamMemberTitle">Filter</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="mb-1" for="filter-category">Category</label>
                        <select id="filter-category" name="filter-category" class="form-select">
                            <option value=""> All </option>
                            @foreach ($data_category as $category)
                                <option value="{{ $category->id }}"> {{ $category->category_name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="mb-1" for="filter-event">Event</label>
                        <select id="filter-event" name="filter-event" class="form-select">
                            @foreach ($data_event as $event)
                                <option value="{{ $event->id }}"
                                    {{ $event->company_code == Auth::user()->company_code ? 'selected' : '' }}>
                                    {{ $event->event_name }} - {{ $event->year }} </option>
                            @endforeach
                            <!-- <option value="" selected> - </option> -->
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal untuk fix all PA --}}
    <div class="modal fade" id="fixModalPA" tabindex="-1" role="dialog" aria-labelledby="rolbackTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title">Fix all Presentation Participant</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-fixall-pa" action="{{ route('assessment.fix.pa') }}" method="POSt">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="mb-3">
                            <input id="fix-all-pa" name="event_id" type="text" hidden>
                            <p>Apakah anda yakin ingin memfiksasi semua penilaian peserta Presentasi?</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit" data-bs-dismiss="modal">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script type="">

        function initializeDataTable(columns) {
            var dataTable = $('#datatable-competition').DataTable({
                "processing": true,
                "serverSide": true,
                "dom": 'lBfrtip',
                "buttons": [
                    'excel', 'csv'
                ],
                "ajax": {
                    "url": "{{ route('query.get_pa_assessment') }}",
                    "type": "GET",
                    "async": false,
                    "dataSrc": function (data) {
                        // console.log(columns);
                        // console.log(data.data);
                        return data.data;
                    },
                    "data": function (d) {
                        d.filterEvent = $('#filter-event').val();
                        // d.filterYear = $('#filter-year').val();
                        d.filterCategory = $('#filter-category').val();
                    }
                },
                "columns": columns,
                "scrollY": true,
                "scrollX": true,
                "stateSave": true,
                "destroy": true
            });
            return dataTable;
        }

        function updateColumnDataTable() {
            newColumn = []
            $.ajax({
                url: "{{ route('query.get_pa_assessment') }}", // Misalnya, URL untuk mengambil kolom yang dinamis
                method: 'GET',
                // dataType: 'json',
                data:{
                    filterEvent: $('#filter-event').val(),
                    // filterYear: $('#filter-year').val(),
                    filterCategory: $('#filter-category').val()
                },
                async: false,
                success: function (data) {
                    if(data.data.length){
                        let row_column = {};
                        row_column['data'] = "DT_RowIndex"
                        row_column['title'] = "No"
                        row_column['mData'] = "DT_RowIndex"
                        row_column['sTitle'] = "No"
                        newColumn.push(row_column)
                        for( var key in data.data[0]){
                            if(key != "DT_RowIndex"){
                                let row_column = {};
                                row_column['data'] = key
                                row_column['title'] = key
                                row_column['mData'] = key
                                row_column['sTitle'] = key
                                newColumn.push(row_column)
                            }
                        }
                    }else{
                        let row_column = {};
                        row_column['data'] = ''
                        row_column['title'] = ''
                        row_column['mData'] = ''
                        row_column['sTitle'] = ''
                        newColumn.push(row_column)
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Gagal mengambil kolom: ' + error);
                }
            });
            return newColumn
        }

        $(document).ready(function() {

            let column = updateColumnDataTable();

            let dataTable = initializeDataTable(column);

            $("#category-pa").val($(`#filter-category`).val())
            $('#fix-all-pa').val($(`#filter-event`).val())

            $('#filter-event').on('change', function () {
                dataTable.destroy();
                dataTable.destroy();

                document.getElementById('datatable-card').insertAdjacentHTML('afterbegin', `<table id="datatable-competition"></table>`);
                $('#fix-all-pa').val($(`#filter-event`).val())

                column = updateColumnDataTable();
                dataTable = initializeDataTable(column);
            });
            $('#filter-category').on('change', function () {
                dataTable.destroy();
                dataTable.destroy();

                document.getElementById('datatable-card').insertAdjacentHTML('afterbegin', `<table id="datatable-competition"></table>`);
                $("#category-pa").val($(`#filter-category`).val())

                column = updateColumnDataTable();
                dataTable = initializeDataTable(column);
            });
        });

        function change_url(id, elementid) {
            //link untuk update
            var form = document.getElementById(elementid);
            var url = `{{ route('paper.rollback', ['id' => ':id']) }}`;
            url = url.replace(':id', id);
            form.action = url;

        }
    </script>
@endpush
