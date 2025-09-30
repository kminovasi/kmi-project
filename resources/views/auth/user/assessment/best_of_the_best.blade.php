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
            padding: 7px;
            font-size: 12px;
        }

        .display thead th,
        .display tbody td {
            border: 0.5px solid #ddd;
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
                            Penetapan Best Of The Best - Penilaian Inovasi
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-xl px-4 mt-4">
        @include('auth.user.paper.navbar')
        
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
                                Tabel Penetapan Best Of The Best
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
                            <form id="datatable-card" method="post"
                                action="{{ route('assessment.determiningTheBestOfTheBestTeam') }}">
                                @csrf
                                @method('PUT')
                                <table id="datatable-best-of-the-best" class="display"></table>
                                <div class="d-flex mt-3 border-top pt-3">
                                    <button type="submit" class="btn btn-primary submit mt-2">Tetapkan</button>
                                </div>
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
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold" id="filterModalLabel">Pengaturan Filter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-floating mb-4">
                            <select id="filter-category" name="filter-category" class="form-select">
                                <option value="" selected>Semua Kategori</option>
                                @foreach ($data_category as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                            <label for="filter-category">Kategori</label>
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
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
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


        var dataTable = $('#datatable-best-of-the-best').DataTable({
            "processing": true,
            "serverSide": true,
            "dom": 'lBfrtip',
            "buttons": [
                'excel', 'csv'
            ],
            "ajax": {
                "url": "{{ route('query.getBestOfTheBest') }}",
                "type": "GET",
                "async": false,
                "dataSrc": function (data) {
                    return data.data;
                },
                "data": function (d) {
                    d.filterEvent = $('#filter-event').val();
                    d.filterCategory = $('#filter-category').val();
                }

            },
            "columns": columns,
            "scrollY": true,
            "scrollX": false,
            "stateSave": true,
            "destroy": true,
            "createdRow": function(row, data, dataIndex) {
                $('thead th').each(function(index) {
                    if ($(this).text().trim() === 'Ranking') {
                        $(row).find('td:nth-child(' + (index + 1) + ')').addClass('text-center'); 
                    }
                });
            }
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
            url: "{{ route('query.getBestOfTheBest') }}", 
            method: 'GET',
            data:{
                filterEvent: $('#filter-event').val(),
                filterCategory: $('#filter-category').val()
            },
            async: false,
            success: function (data) {
                console.log(data.data)
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


        $('#filter-event').on('change', function () {
            dataTable.destroy();
            dataTable.destroy();

            document.getElementById('datatable-card').insertAdjacentHTML('afterbegin', `<table id="datatable-best-of-the-best"></table>`);
            column = updateColumnDataTable();
            dataTable = initializeDataTable(column);
        });
        $('#filter-category').on('change', function () {
            dataTable.destroy();
            dataTable.destroy();

            document.getElementById('datatable-card').insertAdjacentHTML('afterbegin', `<table id="datatable-best-of-the-best"></table>`);

            column = updateColumnDataTable();
            dataTable = initializeDataTable(column);
        });
    });

    function toggleRadio(selectedRadio) {
    $('input[type="radio"][name="pvt_event_team_id"]').click(function() {
            if ($(this).is(':checked')) {
                if ($(this).data('clicked')) {
                    $(this).prop('checked', false); 
                    $(this).data('clicked', false);
                } else {
                    $(this).data('clicked', true); 
                }
            } else {
                $(this).data('clicked', false);
            }
        });
    }

    document.addEventListener('change', function (e) {
      if (!e.target.classList.contains('keputusan-bod-check')) return;
      const input = document.querySelector(e.target.dataset.target);
      if (!input) return;
    
      if (e.target.checked) {
        input.disabled = false;
        input.classList.remove('d-none');  
        input.style.removeProperty('display'); 
        input.focus();
      } else {
        input.value = '';
        input.disabled = true;
        input.classList.add('d-none');     
        input.style.display = 'none';      
      }
    });


</script>
@endpush
