@extends('layouts.app')
@section('title', 'Tambah User | User Management')

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
                <h3 class="card-title">Tambah Pengguna Baru</h3>
            </div>
            <form action="{{ route('management-system.user.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_id">ID Karyawan</label>
                                <input type="text" name="employee_id" class="form-control" id="employee_id" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Nama Pengguna</label>
                                <input type="text" name="username" class="form-control" id="username" required>
                            </div>
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" name="name" class="form-control" id="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email" required>
                            </div>
                            <div class="form-group">
                                <label for="position_title">Jabatan</label>
                                <input type="text" name="position_title" class="form-control" id="position_title">
                            </div>
                            <div class="form-group">
                                <label for="company_code">Kode Perusahaan</label>
                                <input type="text" name="company_code" class="form-control" id="company_code">
                            </div>
                            <div class="form-group">
                                <label for="company_name">Nama Perusahaan</label>
                                <input type="text" name="company_name" class="form-control" id="company_name">
                            </div>
                            <div class="form-group">
                                <label for="directorate_name">Nama Direktorat</label>
                                <input type="text" name="directorate_name" class="form-control" id="directorate_name">
                            </div>
                            <div class="form-group">
                                <label for="group_function_name">Nama Grup Fungsi</label>
                                <input type="text" name="group_function_name" class="form-control" id="group_function_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department_name">Nama Departemen</label>
                                <input type="text" name="department_name" class="form-control" id="department_name">
                            </div>
                            <div class="form-group">
                                <label for="unit_name">Nama Unit</label>
                                <input type="text" name="unit_name" class="form-control" id="unit_name">
                            </div>
                            <div class="form-group">
                                <label for="section_name">Nama Seksi</label>
                                <input type="text" name="section_name" class="form-control" id="section_name">
                            </div>
                            <div class="form-group">
                                <label for="sub_section_of">Sub Seksi Dari</label>
                                <input type="text" name="sub_section_of" class="form-control" id="sub_section_of">
                            </div>
                            <div class="form-group">
                                <label for="date_of_birth">Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" class="form-control" id="date_of_birth">
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Pilih Gender</option>
                                    <option value="Male">Laki-Laki</option>
                                    <option value="Female">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="job_level">Tingkat Pekerjaan</label>
                                <input type="text" name="job_level" class="form-control" id="job_level">
                            </div>
                            <div class="form-group">
                                <label for="contract_type">Jenis Kontrak</label>
                                <input type="text" name="contract_type" class="form-control" id="contract_type">
                            </div>
                            <div class="form-group">
                                <label for="home_company">Perusahaan Asal</label>
                                <input type="text" name="home_company" class="form-control" id="home_company">
                            </div>
                            <div class="form-group">
                                <label for="password">Kata Sandi</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                            </div>
                            <div class="form-group">
                                <label for="role">Peran</label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="">Pilih Peran</option>
                                    <option value="Superadmin">Superadmin</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Pengelola Inovasi">Pengelola Inovasi</option>
                                    <option value="BOD">BOD</option>
                                    <option value="5">5</option>
                                    <option value="User">Pengguna</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="manager_id">ID Manajer (Opsional)</label>
                                <input type="text" name="manager_id" class="form-control" id="manager_id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('management-system.user.index') }}" class="btn btn-secondary">Batal</a>
                </div>

            </form>
        </div>
    </div>
@endsection

