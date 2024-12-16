@extends('layouts.app')
@section('title', 'Role | BOD')
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
                            Data BOD
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('management-system.role.index') }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                        <a class="btn btn-sm btn-primary text-white"
                            href="{{ route('management-system.role.bod.event.create') }}">
                            <i class="me-1" data-feather="plus"></i>
                            Tambah BOD Event
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
        </div>
        <div class="card mb-4">
            <div class="card-body">
                {{-- <div class="mb-3">
                    @if (Auth::user()->role == 'Admin')
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
                    @endif
                </div> --}}
                <table id="datatable-innovator">

                </table>
            </div>

        </div>
    </div>
    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script type="">
$(document).ready(function() {
    let selectedId = null; // Untuk menyimpan ID data yang akan dihapus

    let table = $('#datatable-innovator').DataTable({
        processing: true,
        serverSide: false,
        ajax: "{{ route('bodevent.index') }}",
        columns: [
            { data: 'bod_name', title: 'Nama BOD' },
            { data: 'company_name', title: 'Nama Perusahaan' },
            { data: 'position', title: 'Posisi' },
            { data: 'event_name', title: 'Event' },
            { data: 'event_type', title: 'Tipe Event' }, // Kolom baru untuk tipe event
            { data: 'job_level', title: 'Job Level' },
            {
                data: 'action',
                title: 'Action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary toggle-status-btn" data-id="${row.id}" data-status="${row.status}">
                            ${row.status === 'active' ? 'Non Aktifkan' : 'Aktifkan'}
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}">
                            Delete
                        </button>`;
                }


                },
        ],
    });

    // Tampilkan modal saat tombol delete diklik
   $('#datatable-innovator').on('click', '.delete-btn', function() {
    selectedId = $(this).data('id'); // Simpan ID
    if (!selectedId) {
        alert('Invalid data ID!');
        return;
    }
    $('#deleteModal').modal('show');
});


    // Konfirmasi penghapusan
 $('#confirmDeleteBtn').on('click', function() {
    if (selectedId) {
        $.ajax({
            url: `/bodevent/${selectedId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                alert(response.message);
                table.ajax.reload();
            },
            error: function(xhr) {
                $('#deleteModal').modal('hide');
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    }
});

$('#datatable-innovator').on('click', '.toggle-status-btn', function() {
    const id = $(this).data('id');
    const currentStatus = $(this).data('status');

    if (id) {
        $.ajax({
            url: `/bodevent/toggle-status/${id}`,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert(response.message);
                $('#datatable-innovator').DataTable().ajax.reload();
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    }
});

});
// $('#datatable-innovator').DataTable().ajax.reload();


</script>
@endpush
