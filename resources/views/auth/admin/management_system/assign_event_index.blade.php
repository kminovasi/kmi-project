@extends('layouts.app')
@section('title', 'Data Paper')
@push('css')
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

        .file-review {
            margin: 20px 10px;
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
                            Data Event
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
            @if (session('errors'))
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    {{ session('errors') }}

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="card card-header-actions mb-4">
            <div class="card-header">
                @if (Auth::user()->role == 'Superadmin')
                    <a class="btn btn-sm btn-primary text-white"
                        href="{{ route('management-system.assign.event.create') }}">
                        <i class="me-1" data-feather="plus"></i>
                        Assign Event
                    </a>
                @endif
            </div>
            <div class="card-body">
                {{-- <div class="mb-3">
                    @if (Auth::user()->role == 'Admin')
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
                    @endif
                </div> --}}
                <table id="datatable-events" class="display">

                </table>
            </div>

        </div>
    </div>

    {{-- modal untuk change event --}}
    <div class="modal fade" id="updateEvent" tabindex="-1" role="dialog" aria-labelledby="updateEventTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateEventTitle">Update Data Event</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateDataForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input class="form-control" id="upStatus" type="hidden" name="status">
                        <div class="mb-3">
                            <label for="upEventName">Nama Event</label>
                            <input class="form-control" id="upEventName" type="text" name="event_name">
                        </div>
                        <div class="mb-3">
                            <label for="upCompany">Pilih Perushaan</label>
                            <select name="company_code" id="upCompany" class="form-select">
                                @foreach ($datas_company as $cp)
                                    <option value="{{ $cp->company_code }}">{{ $cp->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="upYear">Pilih Tahun</label>
                            <select name="year" id="upYear" class="form-select">
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="upStartDate">Pilih Tanggal Mulai</label>
                            <input class="form-control" id="upStartDate" type="date" name="start_date">
                        </div>
                        <div class="mb-3">
                            <label for="upEndDate">Pilih Tanggal Berakhir</label>
                            <input class="form-control" id="upEndDate" type="date" name="end_date">
                        </div>
                        <div class="mb-3">
                            <label for="upDescription">Deskrispi</label>
                            <textarea name="description" id="upDescription" cols="30" rows="10" class="form-control"
                                name="data_description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="submit" data-bs-dismiss="modal">Submit</button>
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal untuk change event --}}
    <div class="modal fade" id="changeEvent" tabindex="-1" role="dialog" aria-labelledby="changeEventTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changeEventTitle">Change Status Event</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusEvent" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p>Pilih untuk mengubah status event</p>
                        <label for="not active">
                            <input type="radio" id="not active" name="status" value="not active" required>
                            Not Active
                        </label>
                        <label for="active">
                            <input type="radio" id="active" name="status" value="active" required>
                            Active
                        </label>
                        <label for="finish">
                            <input type="radio" id="finish" name="status" value="finish" required>
                            Finish
                        </label>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="submit" data-bs-dismiss="modal">Submit</button>
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
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
        var dataTable = $('#datatable-events').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('query.get_event') }}",
                "type": "GET",
                "dataSrc": function (data) {
                    return data.data;
                }
            },
            "columns": [
                {
                    "data": null,
                    "title": "No",
                    "render": function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }

                },
                {"data": "event_name", "title": "Event Name"},
                {"data": "company", "title": "Company Name"},
                {"data": "year", "title": "Year"},
                {"data": "date_start", "title": "Date Start"},
                {"data": "date_end", "title": "Date End"},
                {"data": "status", "title": "Status"},
                {"data": "action", "title": "Action"},
            ],

            "scrollY": true,
            "stateSave": true,
        });

    });
    function update_modal(eventId) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',

            url: '{{ route('query.custom') }}',
            data: {
                table: "events",

                where: {
                    "id": eventId
                },
                limit: 1,
                select:[
                    'event_name',
                    'company_code',
                    'date_start',
                    'date_end',
                    'status',
                    'year',
                    'description',
                ]
            },
            // dataType: 'json',
            success: function(response) {
                console.log(response)
                document.getElementById("upEventName").value = response[0].event_name;
                document.getElementById("upStatus").value = response[0].status;
                document.getElementById("upStartDate").value = response[0].date_start;
                document.getElementById("upEndDate").value = response[0].date_end;
                document.getElementById("upDescription").value = response[0].description;

                var selectElement = document.getElementById("upCompany");
                selectElement.value = response[0].company_code;

                for (var i = 0; i < selectElement.options.length; i++) {
                    var option = selectElement.options[i];
                    if (option.value === response[0].company_code) {
                        option.selected = true;
                    } else {
                        option.selected = false;
                    }
                }

                var selectElementYear = document.getElementById("upYear");
                selectElementYear.value = response[0].year.toString();

                for (var x = 0; x < selectElementYear.options.length; x++) {
                    var optionYear = selectElementYear.options[x];
                    if (optionYear.value === response[0].year.toString()) {
                        optionYear.selected = true;
                    } else {
                        optionYear.selected = false;
                    }
                }

                var form = document.getElementById('updateDataForm');
                var url = `{{ route('management-system.update.event', ['id' => ':eventId']) }}`;
                url = url.replace(':eventId', eventId);
                form.action = url;
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        //link untuk update
        // var form = document.getElementById('updatePointAssessment');
        // var url = `{{ route('assessment.update.point', ['id' => ':assessment_point_id']) }}`;
        // url = url.replace(':assessment_point_id', assessment_point_id);
        // form.action = url;

    }
    function set_data_on_modal(event_id){
        var form = document.getElementById('updateStatusEvent');
        var url = `{{ route('management-system.change.event', ['id' => ':event_id']) }}`;
        url = url.replace(':event_id', event_id);
        form.action = url;
    }
</script>
@endpush
