@extends('layouts.app')
@section('title', 'Assessment | Presentasi BOD')
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

        .submit {
            width: 200px;
        }

        .small-input {
            width: 100px;
            /* Sesuaikan lebar sesuai kebutuhan */
            padding: 7px;
            /* Sesuaikan padding sesuai kebutuhan */
            font-size: 12px;
            /* Sesuaikan ukuran font sesuai kebutuhan */
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
                            Assessment - Presentasi BOD
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="p-2">

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
                        Tabel Presentasi BOD
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if (Auth::user()->role == 'Superadmin' || Auth::user()->role == 'Admin' || Auth::user()->role == 'Juri')
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                                    data-bs-target="#filterModal">Filter</button>
                            @endif
                        </div>
                        <div>
                            <form id="datatable-card" method="post" action="{{ route('assessment.keputusanBOD') }}">
                                @csrf
                                @method('POST')
                                <table id="datatable-presentasi-bod" class="display"></table>
                                <button type="submit" class="btn btn-primary submit">Submit</button>
                            </form>
                        </div>
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
        <div class="modal fade" id="executiveSummaryPPT" tabindex="-1" role="dialog"
            aria-labelledby="executiveSummaryPPTTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="executiveSummaryPPTTitle">Uppload PPT Summary Executive</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('assessment.summaryPPT') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="inputId" value="">
                        <input type="hidden" name="pvt_event_teams_id" id="inputEventTeamID">
                        <div class="modal-body">
                            <div class="mb-2">
                                <label for="inputTeamName" class="text-gray-900">Team</label>
                                <div class="small mb-0" id="TeamName"></div>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <label for="InnovationTitle" class="text-gray-900">Innovation Title</label>
                                <div class="small mb-0" id="InnovationTitle"></div>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <label for="Company" class="text-gray-900">Company</label>
                                <div class="small mb-0" id="Company"></div>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <label for="ProblemBackground" class="text-gray-900">Background Masalah</label>
                                <div class="small mb-0" id="ProblemBackground"></div>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <label for="InnovationIdea" class="text-gray-900">Innovation Idea </label>
                                <div class="small mb-0" id="InnovationIdea"></div>

                            </div>
                            <hr>
                            <div class="mb-2">
                                <label for="Benefit" class="text-gray-900">Benefit</label>
                                <div class="small mb-0" id="Benefit"></div>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <label for="uploadPPT" class="text-gray-900">Upload PDF</label>
                                <input type="file" name="file_ppt" id="uploadPPT" class="form-control">
                            </div>
                            <hr>

                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit" data-bs-dismiss="modal">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="ppt" tabindex="-1" aria-labelledby="pptLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pptLabel">PDF Viewer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <iframe id="viewPPT" src="" width="100%" height="500px"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
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

        var dataTable = $('#datatable-presentasi-bod').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                'excel', 'csv'
            ],
            "ajax": {
                "url": "{{ route('query.get_presentasi_bod') }}",
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
            "scrollX": false,
            "stateSave": true,
            "destroy": true
        });
        return dataTable;
    }

    function updateColumnDataTable() {
        newColumn = []
        $.ajax({
            url: "{{ route('query.get_presentasi_bod') }}", // Misalnya, URL untuk mengambil kolom yang dinamis
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

            document.getElementById('datatable-card').insertAdjacentHTML('afterbegin', `<table id="datatable-presentasi-bod"></table>`);
            column = updateColumnDataTable();
            dataTable = initializeDataTable(column);
        });
        $('#filter-category').on('change', function () {
            dataTable.destroy();
            dataTable.destroy();

            document.getElementById('datatable-card').insertAdjacentHTML('afterbegin', `<table id="datatable-presentasi-bod"></table>`);

            column = updateColumnDataTable();
            dataTable = initializeDataTable(column);
        });
    });


    function setSummaryPPT(team_id){
        console.log(team_id);
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
                        'companies':{
                            'companies.company_code' : 'teams.company_code'
                        },
                        'pvt_event_teams':{
                            'pvt_event_teams.team_id' : 'teams.id'
                        },
                        'summary_executives':{
                            'summary_executives.pvt_event_teams_id' : 'pvt_event_teams.id'
                        }
                    },
                select:[
                        'teams.id as team_id',
                        'innovation_title',
                        'team_name',
                        'company_name',
                        'pvt_event_teams.id as pvt_event_teams_id',
                        'summary_executives.id as summary_executives_id',
                        'problem_background',
                        'innovation_idea',
                        'benefit',
                    ]
            },
            // dataType: 'json',
            success: function(response) {
                console.log(response)
                document.getElementById("TeamName").textContent = response[0].team_name;
                document.getElementById("InnovationTitle").textContent = response[0].innovation_title;
                document.getElementById("Company").textContent = response[0].company_name;
                document.getElementById("inputEventTeamID").value = response[0].pvt_event_teams_id;
                document.getElementById("inputId").value = response[0].summary_executives_id;
                document.getElementById("ProblemBackground").textContent = response[0].problem_background;
                document.getElementById("InnovationIdea").textContent = response[0].innovation_idea;
                document.getElementById("Benefit").textContent = response[0].benefit;
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

    }
    function viewPPT(team_id) {
  // Membuka modal dengan id "ppt"
  $('#ppt').modal('show');

  // Mengatur src iframe ke URL file PDF
  $.ajax({
    type: 'GET',
    url: `/path/to/ppt/url/${team_id}`,
    success: function(response) {
      $('#viewPPT').attr('src', response.fileUrl);
    }
  });
}
function seePPT(team_id) {
    console.log(team_id);
        var pptUrl;
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
                    "teams.id": team_id
                },
                limit: 1,
                join: {
                        'pvt_event_teams':{
                            'pvt_event_teams.team_id' : 'teams.id'
                        },
                        'summary_executives':{
                            'summary_executives.pvt_event_teams_id' : 'pvt_event_teams.id'
                        }
                    },
                select:[
                        'teams.id as team_id',
                        'benefit',
                        'summary_executives.file_ppt as file_ppt'
                    ]
            },
            // dataType: 'json',
            success: function(response) {
                console.log(response)
                // document.getElementById("idBenefit").value = response[0].benefit;
                pptUrl =  '{{route('query.getFile')}}' + '?directory=' + response[0].file_ppt;

                // Set the URL as the source for the iframe
                document.getElementById("pptViewer").src = pptUrl;
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
}


</script>
    @endpush
