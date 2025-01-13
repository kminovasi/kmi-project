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
                                <label>Employee ID</label>
                                <input type="text" name="employee_id" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Position Title</label>
                                <input type="text" name="position_title" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Company Code</label>
                                <input type="text" name="company_code" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Company Name</label>
                                <input type="text" name="company_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Directorate Name</label>
                                <input type="text" name="directorate_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Group Function Name</label>
                                <input type="text" name="group_function_name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Department Name</label>
                                <input type="text" name="department_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Unit Name</label>
                                <input type="text" name="unit_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Section Name</label>
                                <input type="text" name="section_name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Sub Section Of</label>
                                <input type="text" name="sub_section_of" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control">
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
                                <label>Job Level</label>
                                <input type="text" name="job_level" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Contract Type</label>
                                <input type="text" name="contract_type" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Home Company</label>
                                <input type="text" name="home_company" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="Superadmin">Superadmin</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Pengelola Inovasi">Pengelola Inovasi</option>
                                    <option value="BOD">BOD</option>
                                    <option value="5">5</option>
                                    <option value="User">User</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Manager ID (Optional)</label>
                                <input type="text" name="manager_id" class="form-control">
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
