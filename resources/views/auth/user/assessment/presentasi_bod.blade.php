@extends('layouts.app')
@section('title', 'Assessment | Presentasi BOD')
@push('css')
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
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
                    Auth::user()->role == 'Superadmin' ||
                    $is_judge)
                <a href="{{ route('assessment.on_desk') }}"
                    class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.on_desk') ? 'active-link' : '' }}">Assessment</a>
            @endif

            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
                <a href="{{ route('paper.event') }}"
                    class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('paper.event') ? 'active-link' : '' }}">Event
                    Group</a>
            @endif

        </div>
        <div class="mb-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
            @endif

        </div>
        @include('auth.user.assessment.bar')
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4 col-sm-8 col-xs-12">
                                Tabel Presentasi BOD
                            </div>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <div id="event-title"></div>
                            </div>
                        </div>
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
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold" id="filterModalLabel">Filter Options</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <!-- Filter Category -->
                        <div class="form-floating mb-4">
                            <select id="filter-category" name="filter-category" class="form-select">
                                <option value="" selected>All Categories</option>
                                @foreach ($data_category as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                            <label for="filter-category">Category</label>
                        </div>

                        <!-- Filter Event -->
                        <div class="form-floating mb-4">
                            <select id="filter-event" name="filter-event" class="form-select"
                                {{ Auth::user()->role == 'Superadmin' ? '' : 'disabled' }}>
                                @foreach ($data_event as $event)
                                    <option name="event_id" value="{{ $event->id }}"
                                        {{ $event->company_code == Auth::user()->company_code ? 'selected' : '' }}>
                                        {{ $event->event_name }} - {{ $event->year }}
                                    </option>
                                @endforeach
                            </select>
                            <label for="filter-event">Event</label>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Apply Filter</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- modal untuk executive summary --}}
        <div class="modal fade" id="executiveSummaryPPT" tabindex="-1" role="dialog" aria-labelledby="executiveSummaryPPTTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="executiveSummaryPPTTitle">Upload PPT Ringkasan Eksekutif</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <form method="POST" action="{{ route('assessment.summaryPPT') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="inputId" value="">
                        <input type="hidden" name="pvt_event_teams_id" id="inputEventTeamID">
                        <div class="modal-body p-4">
                            <!-- Frame for Team Information -->
                            <div class="mb-3 p-3 border rounded shadow-sm">
                                <label for="inputTeamName" class="form-label font-weight-normal">Tim</label>
                                <div id="TeamName" class="font-weight-bold small text-muted"></div>
                            </div>
                            <hr>
                            <!-- Frame for Innovation Title -->
                            <div class="mb-3 p-3 border rounded shadow-sm">
                                <label for="InnovationTitle" class="form-label font-weight-normal">Judul Inovasi</label>
                                <div id="InnovationTitle" class="font-weight-bold small text-muted"></div>
                            </div>
                            <hr>
                            <!-- Frame for Company Information -->
                            <div class="mb-3 p-3 border rounded shadow-sm">
                                <label for="Company" class="form-label font-weight-normal">Perusahaan</label>
                                <div id="Company" class="font-weight-bold small text-muted"></div>
                            </div>
                            <hr>
                            <!-- Frame for Problem Background -->
                            <div class="mb-3 p-3 border rounded shadow-sm">
                                <label for="ProblemBackground" class="form-label font-weight-normal">Latar Belakang Masalah</label>
                                <div id="ProblemBackground" class="font-weight-bold small text-muted"></div>
                            </div>
                            <hr>
                            <!-- Frame for Innovation Idea -->
                            <div class="mb-3 p-3 border rounded shadow-sm">
                                <label for="InnovationIdea" class="form-label font-weight-normal">Ide Inovasi</label>
                                <div id="InnovationIdea" class="font-weight-bold small text-muted"></div>
                            </div>
                            <hr>
                            <!-- Frame for Benefits -->
                            <div class="mb-3 p-3 border rounded shadow-sm">
                                <label for="Benefit" class="form-label font-weight-normal">Manfaat</label>
                                <div id="Benefit" class="font-weight-bold small text-muted"></div>
                            </div>
                            <hr>
                            <!-- Frame for Upload PPT -->
                            <div class="mb-3 p-3 border rounded shadow-sm">
                                <label for="uploadPPT" class="form-label font-weight-normal">Unggah PPT</label>
                                <input type="file" name="file_ppt" id="uploadPPT" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                            <button class="btn btn-primary" type="submit">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal View PDF-->
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
        <script
            src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
        </script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

        <script type="">
    function initializeDataTable(columns) {
        var dataTable = $('#datatable-presentasi-bod').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "dom": 'lBfrtip',
            "buttons": [
                'excel', 'csv'
            ],
            "ajax": {
                "url": "{{ route('query.get_presentasi_bod') }}",
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
            "scrollX": false,
            "stateSave": true,
            "destroy": true
        });
        return dataTable;
    }

    function updateColumnDataTable() {
        const selectElement = document.getElementById('filter-event');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const eventName = selectedOption.text;
        document.getElementById('event-title').innerHTML = eventName;
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


    function setSummaryPPT(eventTeamId){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: `/query/summary-executive/get-summary-executive-by-event-team-id/${eventTeamId}`,
            dataType: 'json',
            // dataType: 'json',
            success: function(response) {
                document.getElementById("TeamName").textContent = response.team_name;
                document.getElementById("InnovationTitle").textContent = response.innovation_title;
                document.getElementById("Company").textContent = response.company_name;
                document.getElementById("inputEventTeamID").value = response.pvt_event_teams_id;
                document.getElementById("inputEventTeamID").value = response.pvt_event_teams_id;
                document.getElementById("inputId").value = response.summary_executives_id;
                document.getElementById("ProblemBackground").textContent = response.problem_background;
                document.getElementById("InnovationIdea").textContent = response.innovation_idea;
                document.getElementById("Benefit").textContent = response.benefit;
                document.getElementById("Benefit").textContent = response.benefit;
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
                // document.getElementById("idBenefit").value = response[0].benefit;
                // document.getElementById("idBenefit").value = response[0].benefit;
                pptUrl =  '{{route('query.getFile')}}' + '?directory=' + response[0].file_ppt;

                // Set the URL as the source for the iframe
                document.getElementById("pptViewer").src = pptUrl;
                document.getElementById("pptViewer").src = pptUrl;
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
}


</script>
    @endpush
