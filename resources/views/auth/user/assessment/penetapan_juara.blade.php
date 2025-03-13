@extends('layouts.app')
@section('title', 'Assessment | Penetapan Juara')
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
                            Penetapan Juara - Penilaian Inovasi
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        {{-- Component Navigation Bar Assessment --}}
        @include('components.assessment.navbar')


        <div class="mb-3">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                    {{-- Cek apakah ada URL untuk tombol --}}
                    @if (session('bodEventUrl'))
                        <a href="{{ session('bodEventUrl') }}" class="btn btn-warning">Buat BOD Event</a>
                    @endif

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>


        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                {{ session('success') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @include('auth.user.assessment.bar')
        <div class="row">
            <div class="col-md-12">
                <div class="card card-header-actions">
                    <div class="card-header row justify-between">
                        <div class="col-md-3 col-sm-4 col-xs-4">
                            Tabel Penetapan Juara
                        </div>
                        <div class="col-md-5 col-sm-6 col-xs-6">
                            <div id="event-title"></div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-2">
                            @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                                    data-bs-target="#addBeritaAcara"><i class="fa fa-plus" aria-hidden="true"></i>
                                    &nbsp;Buat
                                    Berita Acara</button>
                            @endif
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
                            <div id="datatable-card">
                                <table id="datatable-penetapan-juara" class="table"></table>
                            </div>
                            <div id="pengumuman-berita-acara" class="card mb-3 shadow-sm">
                                <div class="card-body">
                                    <table id="datatablesSimple" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Event</th>
                                                <th>Tahun</th>
                                                <th>No. Surat</th>
                                                <th>Opsi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; ?>
                                            @foreach ($data as $d)
                                                <tr>
                                                    <td>{{ $no++ }}</td>
                                                    <td>{{ $d->event_name }}</td>
                                                    <td>{{ $d->year }}</td>
                                                    <td>{{ $d->no_surat }}</td>
                                                    <td>
                                                        <a href="{{ route('berita-acara.showPDF', ['id' => $d->id]) }}" class="btn btn-info btn-sm" target="_blank">Tampilkan</a>
                                                        <a href="{{ route('berita-acara.downloadPDF', ['id' => $d->id]) }}" class="btn btn-primary btn-sm">Unduh</a>
                                                        <form class="mt-2" action="{{ route('berita-acara.destroy', ['id' => $d->id]) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita acara ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- modal untuk berita acara --}}
    <div class="modal fade" id="addBeritaAcara" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Berita Acara</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('berita-acara.store') }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-body">
                        <div class="card mb-3 shadow-sm">
                            <div class="card-header bg-primary text-white font-weight-normal">
                                Form Berita Acara
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="chooseEvent" class="form-label">Pilih Event</label>
                                    <select name="event_id" id="chooseEvent" class="form-select">
                                        @foreach ($data_event as $item)
                                            <option value="{{ $item->id }}">{{ $item->event_name }} - {{ $item->year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="inputNoSurat" class="form-label">No Surat</label>
                                    <input type="text" name="no_surat" id="inputNoSurat" placeholder="Masukkan Nomor Surat" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="chooseJenisEvent" class="form-label">Jenis Event</label>
                                    <select name="jenis_event" id="chooseJenisEvent" class="form-select">
                                        <option value="Internal">Internal</option>
                                        <option value="Grup">Group</option>
                                        <option value="Eksternal">Eksternal</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="inputDate" class="form-label">Tanggal Penetapan Juara</label>
                                    <input type="date" name="penetapan_juara" id="inputDate" class="form-control" required>
                                </div>
                                <div class="card-footer text-end">
                                    <button class="btn btn-primary" type="submit">Buat Berita Acara</button>
                                </div>
                            </div>
                        </div>

                </form>

            </div>
            <div class="modal-footer">

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
                    <h5 class="modal-title fw-bold" id="filterModalLabel">Pengaturan Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <!-- Filter Category -->
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
                <!-- Modal Footer -->
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>

    {{-- modal untuk executive summary --}}
    <div class="modal fade" id="ppt" tabindex="-1" role="dialog" aria-labelledby="pptTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pptTitle">Rangkuman PPT</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pptViewer" width="100%" height="600px" frameborder="0"></iframe>
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
        function initializeDataTable() {
            $('#datatable-penetapan-juara').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('query.get_penetapan_juara') }}",
                    "type": "GET",
                    "dataSrc": function (data) {
                        return data.data;
                    },
                    "data": function (d) {
                        d.filterEvent = $('#filter-event').val();
                        d.filterCategory = $('#filter-category').val();
                    }
                },
                "columns": [
                    { title: "No", data: null, orderable: false },
                    { title: "Tim", data: 'Tim', orderable: true }, // Adjust the column to match the modified DataTable
                    { title: "Judul", data: 'judul' },
                    { title: "Kategori", data: 'kategori' },
                    { title: "Ranking", data: 'Ranking' }
                ],
                "createdRow": function (row, data, dataIndex) {
                    $('td:eq(0)', row).html(dataIndex + 1); // Populate "No" column
                },
                "scrollY": true,
                "scrollX": false,
                "stateSave": true,
                "destroy": true
            });
        }

        function updateColumnDataTable() {

            newColumn = []
            $.ajax({
                url: "{{ route('query.get_penetapan_juara') }}", // Misalnya, URL untuk mengambil kolom yang dinamis
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
                    // console.log(data.data)
                    // console.log(count(data.data));
                    if(data.data.length){
                        for( var key in data.data[0]){
                            let row_column = {};
                            row_column['data'] = key
                            row_column['title'] = key
                            row_column['mData'] = key
                            row_column['sTitle'] = key
                            newColumn.push(row_column)
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
            // console.log("ini yang di fungsi update");
            // console.log(newColumn);
            return newColumn
        }

        $(document).ready(function() {
            // Definisikan dataTable sebagai variabel global
            let dataTable;
            const selectElement = document.getElementById('filter-event');
                const selectedOption = selectElement.options[selectElement.selectedIndex];
                const eventName = selectedOption.text;
                document.getElementById('event-title').innerHTML = eventName;

            // Inisialisasi pertama kali
            function initTable() {
                if ($.fn.DataTable.isDataTable('#datatable-penetapan-juara')) {
                    dataTable.destroy();
                }

                dataTable = $('#datatable-penetapan-juara').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "{{ route('query.get_penetapan_juara') }}",
                        "type": "GET",
                        "dataSrc": function(data) {
                            return data.data;
                        },
                        "data": function(d) {
                            d.filterEvent = $('#filter-event').val();
                            d.filterCategory = $('#filter-category').val();
                        }
                    },
                    "columns": [
                        { title: "No", data: null, orderable: false },
                        { title: "Tim", data: 'Tim', orderable: true },
                        { title: "Judul", data: 'judul' },
                        { title: "Kategori", data: 'kategori' },
                        { title: "Skor Akhir", data: 'final_score' },
                        { title: "Ranking", data: 'Ranking' }
                    ],
                    "createdRow": function(row, data, dataIndex) {
                        $('td:eq(0)', row).html(dataIndex + 1);
                    },
                    "scrollY": true,
                    "scrollX": false,
                    "stateSave": true
                });
            }

            // Inisialisasi awal
            initTable();

            // Event handlers untuk filter
            $('#filter-event, #filter-category').on('change', function() {
                if ($.fn.DataTable.isDataTable('#datatable-penetapan-juara')) {
                    dataTable.destroy();
                }

                // Hapus dan buat ulang tabel
                $('#datatable-penetapan-juara').remove();
                $('#datatable-card').append('<table id="datatable-penetapan-juara" class="table"></table>');

                // Inisialisasi ulang tabel
                initTable();
            });

            // Pengecekan Tabel Pengumuman Juara
            if($('#datatable-penetapan-juara').length && $('#datatable-penetapan-juara').is(':visible')) {
                $('#pengumuman-berita-acara').hide();
            } else {
                $('#pengumuman-berita-acara').show();
            }
        });


        function seePPT(team_id){
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
                    document.getElementById("idBenefit").value = response[0].benefit;
                    pptUrl =  '{{route('query.getFile')}}' + '?directory=' + response[0].file_ppt;

                    // Set the URL as the source for the iframe
                    document.getElementById("pptViewer").src = pptUrl;
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

            console.log(pptUrl);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '{{ route('query.getFile') }}',
                dataType: 'json',
                async: false,
                data: {
                    directory: pptUrl,
                },
                // dataType: 'json',
                success: function(response) {
                    console.log(response)
                    // document.getElementById("idBenefit").value = response[0].benefit;
                    // var pptUrl = '{{ asset("storage/") }}' + '/' + response[0].file_ppt;

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
