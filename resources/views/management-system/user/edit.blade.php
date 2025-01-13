@extends('layouts.app')
@section('title', 'Edit User | User Management')

@section('content')
    <div class="container-fluid">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Pengguna</h3>
            </div>
            <form action="{{ route('management-system.user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ID Karyawan</label>
                                <input type="text" name="employee_id" class="form-control" value="{{ $user->employee_id }}" required>
                            </div>
                            <div class="form-group">
                                <label>Nama Pengguna</label>
                                <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                            </div>
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                            <div class="form-group">
                                <label>Jabatan</label>
                                <input type="text" name="position_title" class="form-control" value="{{ $user->position_title }}">
                            </div>
                            <div class="form-group">
                                <label>Kode Perusahaan</label>
                                <input type="text" name="company_code" class="form-control" value="{{ $user->company_code }}">
                            </div>
                            <div class="form-group">
                                <label>Nama Perusahaan</label>
                                <input type="text" name="company_name" class="form-control" value="{{ $user->company_name }}">
                            </div>
                            <div class="form-group">
                                <label>Nama Direktorat</label>
                                <input type="text" name="directorate_name" class="form-control" value="{{ $user->directorate_name }}">
                            </div>
                            <div class="form-group">
                                <label>Nama Grup Fungsi</label>
                                <input type="text" name="group_function_name" class="form-control" value="{{ $user->group_function_name }}">
                            </div>
                            <div class="form-group">
                                <label>Nama Departemen</label>
                                <input type="text" name="department_name" class="form-control" value="{{ $user->department_name }}">
                            </div>
                            <div class="form-group">
                                <label>Nama Unit</label>
                                <input type="text" name="unit_name" class="form-control" value="{{ $user->unit_name }}">
                            </div>
                            <div class="form-group">
                                <label>Nama Seksi</label>
                                <input type="text" name="section_name" class="form-control" value="{{ $user->section_name }}">
                            </div>
                            <div class="form-group">
                                <label>Sub Seksi</label>
                                <input type="text" name="sub_section_of" class="form-control" value="{{ $user->sub_section_of }}">
                            </div>
                            <div class="form-group">
                                <label>Tingkat Pekerjaan</label>
                                <input type="text" name="job_level" class="form-control" value="{{ $user->job_level }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password (kosongkan untuk mempertahankan password lama)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Kata Sandi</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Peran</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Pilih Peran</option>
                                    <option value="Superadmin" {{ $user->role == 'Superadmin' ? 'selected' : '' }}>Superadmin</option>
                                    <option value="Admin" {{ $user->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="BOD" {{ $user->role == 'BOD' ? 'selected' : '' }}>BOD</option>
                                    <option value="User" {{ $user->role == 'User' ? 'selected' : '' }}>User</option>
                                    <option value="Juri" {{ $user->role == 'Juri' ? 'selected' : '' }}>Juri</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="gender" class="form-control">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" class="form-control" value="{{ $user->date_of_birth }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary mr-2">Perbarui Pengguna</button>
                        <a href="{{ route('management-system.user.index') }}" class="btn btn-danger">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

