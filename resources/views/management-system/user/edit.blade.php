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
                <h3 class="card-title">Edit User</h3>
            </div>
            <form action="{{ route('management-system.user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Employee ID</label>
                                <input type="text" name="employee_id" class="form-control"
                                    value="{{ $user->employee_id }}" required>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="{{ $user->username }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label>Position Title</label>
                                <input type="text" name="position_title" class="form-control"
                                    value="{{ $user->position_title }}">
                            </div>
                            <div class="form-group">
                                <label>Company Code</label>
                                <input type="text" name="company_code" class="form-control"
                                    value="{{ $user->company_code }}">
                            </div>
                            <div class="form-group">
                                <label>Company Name</label>
                                <input type="text" name="company_name" class="form-control"
                                    value="{{ $user->company_name }}">
                            </div>
                            <div class="form-group">
                                <label>Directorate Name</label>
                                <input type="text" name="directorate_name" class="form-control"
                                    value="{{ $user->directorate_name }}">
                            </div>
                            <div class="form-group">
                                <label>Group Function Name</label>
                                <input type="text" name="group_function_name" class="form-control"
                                    value="{{ $user->group_function_name }}">
                            </div>
                            <div class="form-group">
                                <label>Department Name</label>
                                <input type="text" name="department_name" class="form-control"
                                    value="{{ $user->department_name }}">
                            </div>
                            <div class="form-group">
                                <label>Unit Name</label>
                                <input type="text" name="unit_name" class="form-control" value="{{ $user->unit_name }}">
                            </div>
                            <div class="form-group">
                                <label>Section Name</label>
                                <input type="text" name="section_name" class="form-control"
                                    value="{{ $user->section_name }}">
                            </div>
                            <div class="form-group">
                                <label>Sub Section Of</label>
                                <input type="text" name="sub_section_of" class="form-control"
                                    value="{{ $user->sub_section_of }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password (leave blank to keep current password)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="Superadmin">Superadmin</option>
                                    <option value="Admin">Admin</option>
                                    <option value="BOD">BOD</option>
                                    <option value="User">User</option>
                                    <option value="Juri">Juri</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female
                                    </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control"
                                    value="{{ $user->date_of_birth }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="{{ route('management-system.user.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
