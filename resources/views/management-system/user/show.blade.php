@extends('layouts.app')
@section('title', 'Detail User | Portal Inovasi SIG')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Personal Information</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Employee ID</th>
                                        <td>{{ $user->employee_id ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $user->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Username</th>
                                        <td>{{ $user->username ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $user->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Role</th>
                                        <td>{{ $user->role ?? '-' }}</td>
                                    </tr>
                                </table>

                                <h4>Organizational Details</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Position Title</th>
                                        <td>{{ $user->position_title ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Company</th>
                                        <td>{{ $user->company_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Directorate</th>
                                        <td>{{ $user->directorate_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Department</th>
                                        <td>{{ $user->department_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Unit</th>
                                        <td>{{ $user->unit_name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h4>Manager Information</h4>
                                @if ($user->atasan)
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Manager Name</th>
                                            <td>{{ $user->atasan->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Manager Position</th>
                                            <td>{{ $user->atasan->position_title ?? '-' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <p>No manager assigned</p>
                                @endif

                                <h4>Subordinates</h4>
                                @if ($user->bawahan && $user->bawahan->count() > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Position</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user->bawahan as $bawahan)
                                                <tr>
                                                    <td>{{ $bawahan->name ?? '-' }}</td>
                                                    <td>{{ $bawahan->position_title ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <p>No subordinates</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('management-system.user.index') }}" class="btn btn-secondary">Back to User
                            List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
