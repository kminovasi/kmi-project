@extends('layouts.app')
@section('title', 'Assign Judge')
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
                            Assign Judge
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('management-system.assign.juri') }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-xl px-4 mt-4">
        <div class="mb-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}

                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <div class="row justify-content-center">
            <!-- Join Organization-->
            <div class="col-xl-7 col-lg-8 col-md-9 col-sm-11">
                <div class="card border-0 shadow-lg mb-5">
                    <div class="card-body p-3 text-center">
                        <div class="h3 text-primary mb-0">Formulir Penunjukkan Dewan Juri</div>
                    </div>
                    <hr class="m-0" />
                    <div class="card-body p-5">
                        <form action="{{ route('management-system.assign.juri.store') }}" method="POST"
                            id="assign-juri-form">
                            @csrf
                            <div class="mb-4">
                                <h6 class="small mb-1">Juri</h6>
                                <select class="form-select" aria-label="Default select example" name="employee_id"
                                    id="id_juri" value="{{ old('juri') }}" placeholder="Pilih Juri" required>
                                </select>
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1">Event</h6>
                                <input type="text" class="form-control" value="{{ $datas_event->first()->event_name }} - {{ $datas_event->first()->year }}" readonly>
                                <input type="hidden" name="event_id" value="{{ $datas_event->first()->id }}">
                            </div>


                            <div class="mb-4">
                                <h6 class="small mb-1" for="data_description">Deskripsi</h6>
                                <textarea class="form-control" name="description" id="data_description" cols="30" rows="5" required></textarea>
                            </div>

                            <script>
                                document.querySelector('form').addEventListener('submit', function() {
                                    this.description.classList.toggle('is-invalid', !this.description.value);
                                });
                            </script>

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
            search_select2('id_juri')
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
                                    text: item.employee_id + ' - ' + item.name + ' - ' + item
                                        .company_name, // Nama yang akan ditampilkan di kotak seleksi
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

        document.getElementById("assign-juri-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Mencegah pengiriman langsung
            var id_button = document.getElementById("button_submit")
            id_button.setAttribute('disabled', 'true');;
            id_button.innerText = "Process...";

            setTimeout(function() {
                document.getElementById("assign-juri-form").submit();
            }, 1000);
        });
    </script>
@endpush
