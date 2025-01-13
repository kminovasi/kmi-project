@extends('layouts.app')
@section('title', 'Detail User | Portal Inovasi SIG')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Detail Pengguna</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Informasi Pribadi</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>ID Karyawan</th>
                                        <td>{{ $user->employee_id ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama</th>
                                        <td>{{ $user->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Nama Pengguna</th>
                                        <td>{{ $user->username ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $user->email ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Peran</th>
                                        <td>{{ $user->role ?? '-' }}</td>
                                    </tr>
                                </table>

                                <h4>Detail Organisasi</h4>
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Jabatan</th>
                                        <td>{{ $user->position_title ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Perusahaan</th>
                                        <td>{{ $user->company_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Direktorat</th>
                                        <td>{{ $user->directorate_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Departemen</th>
                                        <td>{{ $user->department_name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Unit</th>
                                        <td>{{ $user->unit_name ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h4>Informasi Manajer</h4>
                                @if ($user->atasan)
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Nama Manajer</th>
                                            <td>{{ $user->atasan->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jabatan Manajer</th>
                                            <td>{{ $user->atasan->position_title ?? '-' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <p>Tidak ada manajer yang ditugaskan</p>
                                @endif

                                <h4>Bawahan</h4>
                                @if ($user->bawahan && $user->bawahan->count() > 0)
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Jabatan</th>
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
                                    <p>Tidak ada bawahan</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('management-system.user.index') }}" class="btn btn-primary">Kembali ke Daftar Pengguna</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

