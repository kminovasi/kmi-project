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
                            <div class="form-group mb-3">
                                <label for="employee_id">ID Karyawan</label>
                                <input type="text" name="employee_id" class="form-control" id="employee_id" required>
                                <small class="text-xs text-danger block">Kecuali Karyawan SIG Grup wajib menggunakan nomor KTP</small>
                            </div>
                            <div class="form-group mb-3">
                                <label for="username">Nama Pengguna</label>
                                <input type="text" name="username" class="form-control" id="username" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="name">Nama</label>
                                <input type="text" name="name" class="form-control" id="name" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" id="email" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="position_title">Jabatan</label>
                                <input type="text" name="position_title" class="form-control" id="position_title">
                            </div>
                            <div class="form-group mb-3">
                            <label for="company_code">Kode Perusahaan</label>
                                <select name="company_code" id="company_code" class="form-control">
                                    <option value="">-- Pilih Kode Perusahaan --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->company_code }}" {{ old('company_code') == $company->company_code ? 'selected' : '' }}>
                                            {{ $company->company_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="company_name">Nama Perusahaan</label>
                                <select name="company_name" id="company_name" class="form-control">
                                    <option value="">-- Pilih Nama Perusahaan --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->company_name }}" {{ old('company_name') == $company->company_name ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="directorate_name">Nama Direktorat</label>
                                <input type="text" name="directorate_name" class="form-control" id="directorate_name">
                            </div>
                            <div class="form-group mb-3">
                                <label for="group_function_name">Nama Grup Fungsi</label>
                                <input type="text" name="group_function_name" class="form-control" id="group_function_name">
                            </div>
                            <div class="form-group mb-3">
                                <label for="department_name">Nama Departemen</label>
                                <input type="text" name="department_name" class="form-control" id="department_name">
                            </div>
                            <div class="form-group mb-3">
                                <label for="unit_name">Nama Unit</label>
                                <input type="text" name="unit_name" class="form-control" id="unit_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="section_name">Nama Seksi</label>
                                <input type="text" name="section_name" class="form-control" id="section_name">
                            </div>
                            <div class="form-group mb-3">
                                <label for="sub_section_of">Sub Seksi Dari</label>
                                <input type="text" name="sub_section_of" class="form-control" id="sub_section_of">
                            </div>
                            <div class="form-group mb-3">
                                <label for="date_of_birth">Tanggal Lahir</label>
                                <input type="date" name="date_of_birth" class="form-control" id="date_of_birth">
                            </div>
                            <div class="form-group mb-3">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Pilih Gender</option>
                                    <option value="Male">Laki-Laki</option>
                                    <option value="Female">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="job_level">Tingkat Pekerjaan</label>
                                <input type="text" name="job_level" class="form-control" id="job_level">
                            </div>
                            <div class="form-group mb-3">
                                <label for="contract_type">Status Karyawan</label>
                                <select name="contract_type" class="form-control" id="contract_type">
                                    <option value="">Pilih Status Karyawan</option>
                                    <option value="Karyawan Tetap">Karyawan Tetap</option>
                                    <option value="Karyawan Kontrak / Penugasan / EOD">Karyawan Kontrak / Penugasan / EOD</option>
                                    <option value="Vendor / Rekanan">Vendor / Rekanan</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="home_company">Perusahaan Asal</label>
                                <select name="home_company" id="home_company" class="form-control">
                                    <option value="">-- Pilih Perusahaan Asal --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->company_name }}" {{ old('home_company') == $company->company_name ? 'selected' : '' }}>
                                            {{ $company->company_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">Kata Sandi</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="role">Peran</label>
                                <select name="role" id="role" class="form-control" required>
                                    <option value="">Pilih Peran</option>
                                    <option value="Superadmin">Superadmin</option>
                                    <option value="Admin">Admin</option>
                                    <option value="BOD">BOD</option>
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

    <script>
    const companies = @json($companies);

    const companyCodeSelect = document.getElementById('company_code');
    const companyNameSelect = document.getElementById('company_name');

    companyCodeSelect.addEventListener('change', function() {
        const selectedCode = this.value;
        const company = companies.find(c => c.company_code === selectedCode);
        if (company) {
            companyNameSelect.value = company.company_name;
        } else {
            companyNameSelect.value = '';
        }
    });

    companyNameSelect.addEventListener('change', function() {
        const selectedName = this.value;
        const company = companies.find(c => c.company_name === selectedName);
        if (company) {
            companyCodeSelect.value = company.company_code;
        } else {
            companyCodeSelect.value = '';
        }
    });
</script>

@endsection

