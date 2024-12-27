@extends('layouts.app')
@section('title', 'Data Paper')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
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
                @if (Auth::user()->role == 'Superadmin' ||Auth::user()->role == 'Admin'  )
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

    {{-- modal untuk edit event --}}
    <div class="modal fade" id="updateEvent" tabindex="-1" aria-labelledby="updateEventTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title text-primary fw-semibold" id="updateEventTitle">Update Data Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateDataForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body bg-white">
                        <div class="mb-3">
                            <label for="upEventName" class="form-label fw-semibold">Nama Event</label>
                            <input type="text" class="form-control" id="upEventName" name="event_name" placeholder="Masukkan Nama Event" required>
                        </div>
                        <div class="mb-3">
                            <label for="upType" class="form-label fw-semibold">Tipe Event</label>
                            <select class="form-select" id="upType" name="type" required onchange="handleEditEventTypeChange()">
                                <option value="" selected disabled>Pilih Tipe Event</option>
                                <option value="AP">Anak Perusahaan</option>
                                <option value="internal">Internal</option>
                                <option value="group">Group</option>
                                <option value="national">National</option>
                                <option value="international">International</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="upCompany" class="form-label fw-semibold">Pilih Perusahaan</label>
                            <select class="form-select" id="upCompany" name="company_code[]" required disabled>
                                <option value="select_all">Select All</option>
                                @foreach ($datas_company as $cp)
                                    <option value="{{ $cp->id }}">{{ $cp->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="upStartDate" class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="upStartDate" name="start_date" required>
                            </div>
                            <div class="col-md-6">
                                <label for="upEndDate" class="form-label fw-semibold">Tanggal Berakhir</label>
                                <input type="date" class="form-control" id="upEndDate" name="end_date" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="upYear" class="form-label fw-semibold">Pilih Tahun</label>
                            <select class="form-select" id="upYear" name="year" required>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="upDescription" class="form-label fw-semibold">Deskripsi</label>
                            <textarea class="form-control" id="upDescription" name="description" rows="5" placeholder="Masukkan Deskripsi" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- modal untuk change status event --}}
    <div class="modal fade" id="changeEvent" tabindex="-1" aria-labelledby="changeEventTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title text-primary fw-semibold" id="changeEventTitle">Ubah Status Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusEvent" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body bg-white">
                        <p class="text-muted mb-3">Update status baru untuk event ini:</p>
                        <div class="mb-3">
                            <label for="statusDropdown" class="form-label fw-semibold">Status Event</label>
                            <select id="statusDropdown" name="status" class="form-select" required>
                                <option value="" selected disabled>Pilih Status</option>
                                <option value="not active">Not Active</option>
                                <option value="active">Active</option>
                                <option value="finish">Finish</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')


<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="">
    $(document).ready(function() {
        initializeSelect2();
        $('#updateEvent').on('shown.bs.modal', function () {
            initializeSelect2();
            const selectCompany = $('#upCompany');
            if (selectCompany.hasClass("select2-hidden-accessible")) {
                selectCompany.select2('destroy');
            }
            selectCompany.select2();

        });

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
                {"data": "type", "title": "Type"},
                {"data": "action", "title": "Action"},
            ],

            "scrollY": true,
            "stateSave": true,
        });

    });
    function handleEditEventTypeChange() {
    const eventType = document.getElementById('upType').value;
    const selectCompany = $('#upCompany');

    // First destroy existing Select2 instance
    if (selectCompany.hasClass("select2-hidden-accessible")) {
        selectCompany.select2('destroy');
    }

    if (eventType) {
        selectCompany.prop('disabled', false);
        selectCompany.find('option').show();

        if (eventType === 'AP') {
            selectCompany.prop('multiple', false);
            selectCompany.find('option[value="select_all"]').hide();
        } else {
            selectCompany.prop('multiple', true);
            selectCompany.find('option[value="select_all"]').show();
        }

        // Re-initialize Select2 with proper configuration
        selectCompany.select2({
            width: '100%',
            dropdownParent: $('#updateEvent'),
            closeOnSelect: eventType === 'AP'
        });

    } else {
        selectCompany.prop('disabled', true);
    }
}





    function update_modal(eventId) {
        set_data_on_modal_event(eventId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            url: '{{ route('query.custom') }}',
            data: {
                table: "events",
                where: { "id": eventId },
                limit: 1,
                select: [
                    'event_name', 'date_start', 'date_end', 'status', 'year', 'description', 'type'
                ]
            },
            success: function(response) {
                document.getElementById("upEventName").value = response[0].event_name;
                document.getElementById("upStartDate").value = response[0].date_start;
                document.getElementById("upEndDate").value = response[0].date_end;
                document.getElementById("upDescription").value = response[0].description;
                document.getElementById("upType").value = response[0].type;

                var selectElement = document.getElementById("upCompany");
                selectElement.value = response[0].company_id;

                for (var i = 0; i < selectElement.options.length; i++) {
                    var option = selectElement.options[i];
                    if (option.value === response[0].company_id) {
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

                // Trigger the event type change handler to ensure the company select is updated
                handleEditEventTypeChange();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function set_data_on_modal_event(event_id) {
        var form = document.getElementById('updateDataForm');
        var url = `{{ route('management-system.update.event', ['id' => ':event_id']) }}`;
        url = url.replace(':event_id', event_id);
        form.action = url;
    }

    function set_data_on_modal(event_id) {
        var form = document.getElementById('updateStatusEvent');
        var url = `{{ route('management-system.change.event', ['id' => ':event_id']) }}`;
        url = url.replace(':event_id', event_id);
        form.action = url;
    }
    function initializeSelect2() {
    const selectCompany = $('#upCompany');
    if (selectCompany.hasClass("select2-hidden-accessible")) {
        selectCompany.select2('destroy');
    }
    selectCompany.select2({
        width: '100%',
        dropdownParent: $('#updateEvent') // This ensures the dropdown appears over the modal
    });
}
</script>
@endpush
