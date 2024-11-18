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
                            <div class="page-header-icon"><i data-feather="book-open"></i></div>
                            Assign BOD Event
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary d-flex align-items-center gap-2"
                            href="{{ route('management-system.role.bod.index') }}">
                            <i data-feather="arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-xl px-4 mt-4">
        <div class="row justify-content-center">
            <!-- Alert messages -->
            <div class="col-12 col-lg-8">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('errors'))
                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                        {{ session('errors') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <!-- Form Assign -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom-0">
                        <h5 class="text-secondary fw-bold mb-0">Form Assign BOD Event</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('management-system.role.bod.event.store') }}" method="POST"
                            id="assign-bod-form">
                            @csrf
                            <div class="mb-4">
                                <label for="id_bod" class="form-label text-muted">Board of Director</label>
                                <select class="form-select" name="employee_id" id="id_bod" value="{{ old('bod') }}"
                                    required>
                                    <option value="" disabled selected>Pilih User</option>
                                    <!-- Options will be dynamically populated -->
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="event_id" class="form-label text-muted">Event</label>
                                <select class="form-select" name="event_id" value="{{ old('event_id') }}" required>
                                    <option value="" disabled selected>Pilih Event</option>
                                    @foreach ($datas_event as $ev)
                                        <option value="{{ $ev->id }}">{{ $ev->event_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary d-flex align-items-center justify-content-center gap-2"
                                    type="submit" id="button_submit">
                                    <i data-feather="send"></i> Submit
                                </button>
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
            search_select2('id_bod')
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
                    url: '{{ route('query.get_BOD') }}',
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
                                    text: item.employee_id + ' - ' + item
                                        .name, // Nama yang akan ditampilkan di kotak seleksi
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
