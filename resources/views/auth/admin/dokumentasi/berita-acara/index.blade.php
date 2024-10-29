@extends('layouts.app')
@section('title', 'Dokumentasi | Berita Acara')
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

        #stickyNav .nav-item {
            margin-bottom: 10px;
            font-size: 16px;
        }

        .btn-group .btn {
            margin-right: 5px;
        }

        .active-link-nav {
            background-color: rgb(232, 21, 0, 0.5);
        }

        .active-link-nav a {
            color: white;
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
                            <div class="page-header-icon"><i data-feather="file-text"></i></div>
                            DOKUMENTASI - BERITA ACARA
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('dokumentasi.index') }}">
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
            @if (session('errors'))
                <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                    {{ session('errors') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header">Berita Acara</div>
                    <div class="card-body">
                        <?php
                        $no = 1;
                        ?>
                        <table id="datatable-beritaacara"></table>
                    </div>
                </div>
                <!-- Modal-->
                <div class="modal fade" id="upload" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Form Berita Acara</h5>
                                <button class="btn-close" type="button" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="formUpdateBeritaacara" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">

                                    <div class="mb-3">
                                        <label for="uploadPDF">No Surat</label>
                                        <input type="file" name="signed_file" id="uploadPDF" class="form-control"
                                            accept=".pdf" required>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                                    <button class="btn btn-primary" type="submit">Save changes</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var dataTable = $('#datatable-beritaacara').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "{{ route('query.get.berita_acara') }}",
                    "type": "GET",
                    "dataSrc": function(data) {
                        return data.data;
                    },
                },
                "columns": [{
                        "data": "DT_RowIndex",
                        "title": "No",
                        "className": "text-center"
                    },
                    {
                        "data": "event_name",
                        "title": "Nama Event",
                        "className": "text-center"
                    },
                    {
                        "data": "no_surat",
                        "title": "Nomor Surat",
                        "className": "text-center"
                    },
                    {
                        "data": "jenis_event",
                        "title": "Jenis Event",
                        "className": "text-center"
                    },
                    {
                        "data": "penetapan_juara",
                        "title": "Penetapan Juara",
                        "className": "text-center"
                    },
                    {
                        "data": "upload",
                        "title": "Upload",
                        "orderable": false,
                        "searchable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "delete",
                        "title": "Hapus",
                        "orderable": false,
                        "searchable": false,
                        "className": "text-center"
                    },
                    {
                        "data": "view",
                        "title": "Lihat",
                        "orderable": false,
                        "searchable": false,
                        "className": "text-center"
                    },
                ],
                "scrollY": "50vh", // Scroll vertikal terbatas
                "scrollCollapse": true,
                "paging": true, // Aktifkan paging
                "searching": true, // Aktifkan searching
                "ordering": true, // Aktifkan ordering
                "pageLength": 10, // Set default jumlah data per halaman
                "lengthChange": false, // Tidak memungkinkan pengguna mengubah jumlah data per halaman
                "language": {
                    "emptyTable": "Tidak ada data yang tersedia",
                    "processing": "Memproses...",
                    "paginate": {
                        "previous": "<",
                        "next": ">"
                    }
                },
                "dom": 'Bfrtip', // Menggunakan DOM dengan tombol export
                "buttons": [{
                        extend: 'excel',
                        text: 'Export to Excel',
                        className: 'btn btn-success btn-sm'
                    },
                    {
                        extend: 'pdf',
                        text: 'Export to PDF',
                        className: 'btn btn-danger btn-sm'
                    }
                ]
            });
        });

        function modal_update_beritaacara(idEventTeam) {
            var form = document.getElementById('formUpdateBeritaacara');
            var url = `{{ route('dokumentasi.berita-acara.upload', ['id' => ':idEventTeam']) }}`;
            url = url.replace(':idEventTeam', idEventTeam);
            form.action = url;
        }
    </script>
@endpush
