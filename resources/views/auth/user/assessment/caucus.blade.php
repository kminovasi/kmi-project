@extends('layouts.app')
@section('title', 'Data Paper')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <style type="text/css">
        .step-one h1 {
            text-align: center;
        }

        .step-one img {
            width: 75%;
            height: 75%;
        }

        .step-one p {
            text-align: justify;
        }

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
                            Assessment - Caucus
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="p2">
            <!-- <a href="{{ route('paper.index') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.index') ? 'active-link' : '' }}">Paper</a>
                                                                                                                                                <a href="{{ route('paper.register.team') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.register.team') ? 'active-link' : '' }}">Register</a>
                                                                                                                                                @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
    <a href="{{ route('assessment.caucus.data') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.caucus.data') ? 'active-link' : '' }}">Assessment</a> -->
            <!-- <a href="" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1">Event</a> -->
            <!-- <a href="{{ route('paper.event') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.event') ? 'active-link' : '' }}">Event</a>
@elseif(Auth::user()->role == 'Juri')
    <a href="{{ route('assessment.caucus.data') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.caucus.data') ? 'active-link' : '' }}">Assessment</a>
    @endif -->

            <a href="{{ route('paper.register.team') }}"
                class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.register.team') ? 'active-link' : '' }}">Register</a>

            <a href="{{ route('paper.index') }}"
                class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.index') ? 'active-link' : '' }}">Makalah
                Inovasi</a>

            @if (Auth::user()->role == 'Juri' ||
                    Auth::user()->role == 'BOD' ||
                    Auth::user()->role == 'Admin' ||
                    Auth::user()->role == 'Superadmin')
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
        </div>
        @include('auth.user.assessment.bar')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Tabel Caucus
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if (Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin' || Auth::user()->role == 'Juri')
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                                    data-bs-target="#filterModal">Filter</button>
                            @endif
                        </div>
                        <form id="datatable-card" action="{{ route('assessment.addBODvalue') }}" method="post">
                            @csrf
                            <div>
                                <table id="datatable-caucus" class="display"></table>
                                {{-- <button class="btn btn-primary" type="submit">submit</button> --}}
                            </div>
                            <input type="text" class="form-control" name="category" id="category-pa" hidden>
                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary next shadow-sm">Submit</button>
                                    <button type="button" class="btn btn-outline-primary next shadow-sm"
                                        data-bs-toggle="modal" data-bs-target="#fixModalPA">Submit All</button>
                                </div>
                            @endif
                        </form>
                    </div>
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
                        {{-- <div class="md-3">
                    <input type="text" name="event_id" id="IDEvent" value="">
                </div> --}}
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
                            <select id="filter-event" name="filter-event" class="form-select"
                                {{ Auth::user()->role == 'Superadmin' ? '' : 'disabled' }}>
                                @foreach ($data_event as $event)
                                    <option name="event_id" value="{{ $event->id }}"
                                        {{ $event->company_code == Auth::user()->company_code ? 'selected' : '' }}>
                                        {{ $event->event_name }} - {{ $event->year }} </option>
                                @endforeach
                                <!-- <option value="" selected> - </option> -->
                            </select>
                            {{-- <input type="text" name="event_id" id="" value="{{ $event->id }}"> --}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal untuk executive summary --}}
        <div class="modal fade" id="executiveSummary" tabindex="-1" role="dialog"
            aria-labelledby="executiveSummaryTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="executiveSummaryTitle">Form Executive Summary</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formExecutiveSummary" method="POST" action="{{ route('assessment.summaryExecutive') }}">
                        @csrf
                        <input type="hidden" name="pvt_event_teams_id" id="inputEventTeamID" value="">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="inputTeamName" class="">Team</label>
                                <input type="text" name="" id="TeamName" class="form-control" value=""
                                    readonly>
                            </div>
                            <div class="mb-3">
                                <label for="InnovationTitle" class="">Innovation Title</label>
                                <input type="text" name="" id="InnovationTitle" class="form-control"
                                    value="" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="Company" class="">Company</label>
                                <input name="" id="Company" name="" class="form-control" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="inputProblemBackground" class="">Background Masalah</label>
                                <textarea name="problem_background" id="inputProblemBackground" cols="15" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="inputInnovationIdea" class="">Innovation Idea </label>
                                <textarea name="innovation_idea" id="inputInnovationIdea" cols="15" rows="5" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="inputBenefit" class="">Benefit</label>
                                <textarea name="benefit" id="inputBenefit" cols="15" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit" data-bs-dismiss="modal">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endsection


    @push('js')
        {{-- <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script> --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js"
            crossorigin="anonymous"></script>
        <script src="js/datatables/datatables-simple-demo.js"></script>
        <script src="js/scripts.js"></script>
        <script type="">
    function initializeDataTable(columns) {

        var dataTable = $('#datatable-caucus').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                'excel', 'csv'
            ],
            "ajax": {
                "url": "{{ route('query.get_caucus') }}",
                "type": "GET",
                "async": false,
                "dataSrc": function (data) {
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
            url: "{{ route('query.get_caucus') }}", // Misalnya, URL untuk mengambil kolom yang dinamis
            method: 'GET',
            // dataType: 'json',
            data:{
                filterEvent: $('#filter-event').val(),
                // filterYear: $('#filter-year').val(),
                filterCategory: $('#filter-category').val()
            },
            async: false,
            success: function (data) {
                // newColumn = []
                console.log(data.data)
                // console.log(count(data.data));
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
        console.log("ini yang di fungsi update");
        console.log(newColumn);
        return newColumn
    }

    $(document).ready(function() {

        let column = updateColumnDataTable();

        let dataTable = initializeDataTable(column);

        $('#filter-event').on('change', function () {
            dataTable.destroy();
            dataTable.destroy();

            document.getElementById('datatable-card').insertAdjacentHTML('afterbegin', `<table id="datatable-caucus"></table>`);
            column = updateColumnDataTable();
            dataTable = initializeDataTable(column);
        });
        $('#filter-category').on('change', function () {
            dataTable.destroy();
            dataTable.destroy();

            document.getElementById('datatable-card').insertAdjacentHTML('afterbegin', `<table id="datatable-caucus"></table>`);

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
    function setSummary(team_id, pvt_event_teams_id){
        // console.log(team_id);
        var pvtEventTeamId;

        document.getElementById("TeamName").value = "";
        document.getElementById("InnovationTitle").value = "";
        document.getElementById("Company").value = "";
        document.getElementById("inputEventTeamID").value = "";
        document.getElementById("inputProblemBackground").value = "";
        document.getElementById("inputInnovationIdea").value = "";
        document.getElementById("inputBenefit").value = "";

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: '{{ route('query.custom') }}',
            dataType: 'json',
            async: false,
            data: {
                table: "teams",
                where: {
                    "teams.id": team_id,
                    "pvt_event_teams.id": pvt_event_teams_id
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
                        'companies':{
                            'companies.company_code' : 'teams.company_code'
                        },
                        'pvt_event_teams':{
                            'pvt_event_teams.team_id' : 'teams.id'
                        },
                    },
                select:[
                        'teams.id as team_id',
                        'innovation_title',
                        'team_name',
                        'company_name',
                        'pvt_event_teams.id as pvt_event_teams_id',
                    ]
            },
            // dataType: 'json',
            success: function(response) {
                console.log("tim", response)
                document.getElementById("TeamName").value = response[0].team_name;
                document.getElementById("InnovationTitle").value = response[0].innovation_title;
                document.getElementById("Company").value = response[0].company_name;
                document.getElementById("inputEventTeamID").value = response[0].pvt_event_teams_id;
                pvtEventTeamId = response[0].pvt_event_teams_id;
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: '{{ route('query.custom') }}',
            dataType: 'json',
            async: false,
            data: {
                table: "summary_executives",
                where: {
                    "summary_executives.pvt_event_teams_id": pvtEventTeamId
                },
                limit: 1,
                select:[
                        'problem_background',
                        'innovation_idea',
                        'benefit'
                    ]
            },
            // dataType: 'json',
            success: function(response) {
                console.log(response)

                document.getElementById("inputProblemBackground").value = response[0].problem_background;
                document.getElementById("inputInnovationIdea").value = response[0].innovation_idea;
                document.getElementById("inputBenefit").value = response[0].benefit;
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

</script>
    @endpush
