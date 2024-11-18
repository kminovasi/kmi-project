@extends('layouts.app')
@section('title', 'Management System | Kategori')
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
    <style type="text/css">
        .active-link-nav {
            background-color: rgba(13, 110, 253, 0.75);
            /* Warna biru Bootstrap bg-primary dengan opacity 75% */
        }

        .active-link-nav a {
            color: white;
            /* Agar teks tetap terlihat jelas */
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
                            <div class="page-header-icon"><i data-feather="grid"></i></div>
                            MANAGEMENT SYSTEM
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
        </div>
        <div class="row">
            <div class="col-lg-9">
                <div class="card card-header-actions mb-4">
                    <div class="card-header">
                        Tabel Kategori
                        <button class="btn btn-primary text-white btn-sm" type="button" data-bs-toggle="modal"
                            data-bs-target="#createCategory"><i class="fa fa-plus" aria-hidden="true"></i> &nbsp; Tambah
                            Kategori</button>
                    </div>
                    <div class="card-body">
                        <table id="datatable-category" class="display">
                        </table>
                    </div>
                </div>
            </div>
            <!-- Sticky Nav-->
            @include('auth.admin.management_system.team.rightbar')
        </div>
    </div>
    <!-- Your HTML content above -->

    {{-- Modal for create category --}}
    <div class="modal fade" id="createCategory" tabindex="-1" role="dialog" aria-labelledby="createCategoryLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Form Kategori</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('management-system.team.category.store') }}" method="POST">
                    @csrf @method('POST')
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="inputNamaKategori" class="form-label text-muted">Nama Kategori</label>
                            <input type="text" name="category_name" id="inputNamaKategori"
                                class="form-control rounded-3 shadow-sm" placeholder="Isi Nama Kategori" required>
                        </div>
                        <div class="mb-4">
                            <label for="inputJenis" class="form-label text-muted">Jenis Kategori</label>
                            <select name="category_parent" id="inputJenis" class="form-select rounded-3 shadow-sm">
                                <option value="BREAKTHROUGH INNOVATION">BREAKTHROUGH INNOVATION</option>
                                <option value="INCREMENTAL INNOVATION">INCREMENTAL INNOVATION</option>
                                <option value="IDEA BOX">IDEA BOX</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-1" type="submit"
                            style="font-weight: 600;">
                            <i data-feather="save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>

        </div>
        <script>
            feather.replace();
        </script>
    </div>
    <!-- Bootstrap Modal for Update -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold">Update Kategori</h5>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateFormCategory" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" class="form-control">
                        <div class="mb-4">
                            <label for="inNamaKategori" class="form-label text-muted">Nama Kategori</label>
                            <input type="text" name="category_name" id="inNamaKategori"
                                class="form-control rounded-3 shadow-sm" required>
                        </div>
                        <div class="mb-4">
                            <label for="inJenis" class="form-label text-muted">Jenis Kategori</label>
                            <select name="category_parent" id="inJenis" class="form-select rounded-3 shadow-sm">
                                <option value="BREAKTHROUGH INNOVATION">BREAKTHROUGH INNOVATION</option>
                                <option value="INCREMENTAL INNOVATION">INCREMENTAL INNOVATION</option>
                                <option value="IDEA BOX">IDEA BOX</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button class="btn btn-primary px-4 py-2 d-flex align-items-center gap-1" type="submit">
                            <i data-feather="save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>

            <script>
                feather.replace();
            </script>

        </div>
    </div>

    {{-- modal untuk delete category --}}
    {{-- <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formDeleteCategory" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="deleteModalTitle">Konfirmasi Hapus Data</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center">
                            <i class="fa fa-exclamation-triangle text-warning" style="font-size: 40px;"></i>
                            <p class="mt-3">Apakah Anda yakin ingin menghapus kategori ini?</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger" style="font-weight: 600;">Hapus Kategori</button>
                    </div>
                </div>
            </form>
        </div>
    </div> --}}

@endsection

@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var dataTable = $('#datatable-category').DataTable({
                "processing": true,
                "serverSide": false,
                "ajax": {
                    "url": '{{ route('query.custom') }}',
                    "type": "GET",
                    "dataType": "json",
                    "data": {
                        table: 'categories',
                        limit: 100,
                    },
                    "dataSrc": ""
                },
                "columns": [{
                        "data": null,
                        "title": "No",
                        "render": function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        "data": "category_name",
                        "title": "Nama Kategori"
                    },
                    {
                        "data": "category_parent",
                        "title": "Jenis Kategori"
                    },
                    {
                        "data": null,
                        "title": "Action",
                        "render": function(data, type, row) {
                            return `
                                <button class="btn btn-warning btn-xs" type="button" data-bs-toggle="modal" data-bs-target="#updateModal" onclick="updateCategory(${row.id})">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </button>
                                <button class="btn btn-danger btn-xs" type="button" onclick="deleteCategory(${row.id})">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            `;
                        }
                    }
                ],
                "scrollY": true,
                "scrollX": false,
                "stateSave": true,
            });
        });

        function updateCategory(categoryId) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                url: '{{ route('query.custom') }}',
                data: {
                    table: "categories",
                    where: {
                        "id": categoryId
                    },
                    limit: 1
                },
                success: function(response) {
                    document.getElementById("id").value = response[0].id;
                    document.getElementById("inNamaKategori").value = response[0].category_name;
                    var selectElement = document.getElementById("inJenis");
                    selectElement.value = response[0].category_parent;
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

            var form = document.getElementById('updateFormCategory');
            var url = `{{ route('management-system.team.category.update', ['id' => ':categoryId']) }}`;
            url = url.replace(':categoryId', categoryId);
            form.action = url;
        }

        function deleteCategory(categoryId) {
            var url = `{{ route('management-system.team.category.delete', ['id' => ':categoryId']) }}`;
            url = url.replace(':categoryId', categoryId);

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Berhasil!',
                                'Kategori berhasil dihapus.',
                                'success'
                            );
                            $('#datatable-category').DataTable().ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus kategori.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush
