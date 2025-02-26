@extends('layouts.app')

@section('title', 'Manajemen Sistem | Pengguna')

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.7/b-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manajemen Pengguna</h3>
                <div class="card-tools">
                    <a href="{{ route('management-system.user.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambahkan Pengguna
                    </a>
                </div>
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

            </div>
            <div class="card-body">
                <table id="userTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Karyawan</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Posisi</th>
                            <th>Manajer</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.7/b-2.4.2/r-2.5.0/datatables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#userTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('management-system.user.data') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'employee_id',
                        name: 'employee_id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'position_title',
                        name: 'position_title'
                    },
                    {
                        data: 'manager_name',
                        name: 'atasan.name'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            });
        });

        $(document).on('click', '.delete-user', function() {
            var userId = $(this).data('id');
            if (confirm('Are you sure you want to delete this user?')) {
                $.ajax({
                    url: '{{ route('management-system.user.destroy', '') }}/' + userId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#userTable').DataTable().ajax.reload();
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        alert('Failed to delete user: ' + xhr.responseJSON.message);
                    }
                });
            }
        });
    </script>
@endpush
