@extends('layouts.app')
@section('title', 'Assign BOD')
@push('css')
    {{-- <link href="{{ asset('template/dist/css/styles.css') }}" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    {{-- <link href="{{ asset('../css/register.css') }}" rel="stylesheet" /> --}}
@endpush
@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Assign Role
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('management-system.role.index') }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-xl px-4 mt-4">

            <div class="row justify-content-center">
                <!-- Join Organization-->
                <div class="div mb-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                            {{ session('success') }}

                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if(session('errors'))
                        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                            {{ session('errors') }}

                            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                </div>

            <div class="col-xl-7 col-lg-8 col-md-9 col-sm-11">
                <div class="card border-0 shadow-lg mb-5">
                    <div class="card-body p-3 text-center">
                        <div class="h3 text-primary mb-0">Form Assign Role</div>
                    </div>
                    <hr class="m-0" />
                    <div class="card-body p-5">
                        <form action="{{ route('management-system.role.assign.store') }}" method="POST"
                            id="assign-bod-form">
                            @csrf
                            @method('put')
                            <div class="mb-4">
                                <h6 class="small mb-1">User</h6>
                                <select class="form-select" aria-label="Default select example"
                                    name="employee_id" id="id_employee" value="{{ old('bod') }}"
                                    placeholder="Pilih User" required>
                                </select>
                            </div>

                            <div class="mb-4">
                                <h6 class="small mb-1" for="data_description">Pilih Role</h6>
                                <select name="role" class="form-control">
                                    @if(auth()->user()->role == 'Superadmin')
                                        <option value="BOD">BOD</option>
                                        <option value="Superadmin">Superadmin</option>
                                        <option value="Admin">Admin</option>
                                        <option value="User">Innovator</option>
                                    @elseif(auth()->user()->role == 'Admin')
                                        <option value="BOD">BOD</option>
                                        <option value="Admin">Admin</option>
                                        <option value="User">Innovator</option>
                                    @endif
                                </select>
                            </div>
                            <!-- Save changes button-->
                            <div class="d-grid">
                                <button class="btn btn-primary" type="submit" id="button_submit">
                                    Submit</button>
                                <!-- <a class="btn btn-secondary">Save</a> -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
        </script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        {{-- <!-- <script src="{{asset('template/dist/js/scripts.js')}}"></script> --> --}}
        <script>
            // fungsi yang dijalankan ketika dom sudah terload
            // akan menginisasi semua select field agar menggunakkan library select2
            document.addEventListener("DOMContentLoaded", function() {
                var selectElements = document.querySelectorAll('select');
                selectElements.forEach(function(select) {

                });
                search_select2('id_employee')
            });


            // fungsi select2 untuk opsi yang membutuhkan data karyawan (fasilitator, leader, anggota)
            function search_select2(select_element_id) {

                $('#' + select_element_id).select2({
                    // allowClear: true,
                    // theme: "classic",
                    allowClear: true,
                    width: "100%",
                    placeholder: "Pilih " + select_element_id.split("_")[1] + (select_element_id.split("_")[2] ? " " +
                        select_element_id.split("_")[2] + " : " : " : "),
                    ajax: {
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'POST', // Metode HTTP POST
                        url: '{{ route('query.autocomplete') }}',
                        dataType: 'json',
                        delay: 250, // Penundaan dalam milidetik sebelum permintaan AJAX dikirim
                        data: function(params) {
                            // Data yang akan dikirim dalam permintaan POST
                            return {
                                query: params.term // Menggunakan nilai input "query" sebagai parameter
                            };
                        },
                        processResults: function(data) {
                            // Memformat data yang diterima untuk format yang sesuai dengan Select2
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.employee_id + ' - ' + item.name + ' - ' + item.company_name, // Nama yang akan ditampilkan di kotak seleksi
                                        id: item.employee_id // Nilai yang akan dikirimkan saat opsi dipilih
                                    };
                                })
                            };
                        },
                        cache: true
                    }
                });
            }

            function loading(element) {
                var id = element.id
                element.setAttribute('disabled', 'true');
                element.innerText = "Process...";
            }

            document.getElementById("assign-bod-form").addEventListener("submit", function(event) {
                event.preventDefault(); // Mencegah pengiriman langsung
                var id_button = document.getElementById("button_submit")
                id_button.setAttribute('disabled', 'true');;
                id_button.innerText = "Process...";

                setTimeout(function() {
                    document.getElementById("assign-bod-form").submit();
                }, 1000);
            });
        </script>
@endpush
