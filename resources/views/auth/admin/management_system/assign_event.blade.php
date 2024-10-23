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
                            Assign Event
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('management-system.assign.event') }}">

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
            <div class="mb-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                        {{ session('success') }}
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('errors'))
                    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                        {{ session('errors') }}
                        <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>
            <!-- Join Organization-->
            <div class="col-xl-7 col-lg-8 col-md-9 col-sm-11">
                <div class="card border-0 shadow-lg mb-5">
                    <div class="card-body p-3 text-center">
                        <div class="h3 text-primary mb-0">Form Add Event</div>
                    </div>
                    <hr class="m-0" />
                    <div class="card-body p-5">
                        <form action="{{ route('management-system.assign.event.store') }}" method="POST"
                            id="assign-juri-form">
                            @csrf
                            <div>
                                <div class="mb-4">
                                    <h6 class="small mb-1">Company</h6>
                                    <select id="selectCompany" class="form-select" aria-label="Default select example"
                                        name="company_code[]" required>
                                        @foreach ($datas_company as $cp)
                                            <option value="{{ $cp->company_code }}">{{ $cp->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <h6 class="small mb-1">Event Name</h6>
                                    <input class="form-control" name="event_name" value="{{ old('event_name') }}"
                                        id="inputEventName" type="text" placeholder="Masukkan Nama Event" required />
                                </div>

                                <div class="mb-4">
                                    <h6 class="small mb-1">Start Date</h6>
                                    <input class="form-control" type="date" id="start_date" name="start_date"
                                        onchange="cek_date()" required>
                                    <div class="invalid-feedback">
                                        Tanggal mulai tidak boleh setelah tanggal berakhir.
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="small mb-1">End Date</h6>
                                    <input class="form-control" type="date" id="end_date" name="end_date"
                                        onchange="cek_date()" required>
                                    <div class="invalid-feedback">
                                        Tanggal berakhir tidak boleh sebelum tanggal mulai.
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="small mb-1">Year</h6>
                                    <select name="year" id="year" class="form-control" required>
                                        @foreach ($years as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <h6 class="small mb-1" for="data_description">Description</h6>
                                    <textarea class="form-control" name="description" id="data_description" cols="30" rows="5" required></textarea>
                                    @if ($errors->has('description'))
                                        <div class="text-danger">{{ $errors->first('description') }}</div>
                                    @endif
                                </div>

                                <!-- Save changes button-->
                                <div class="d-grid">
                                    <button class="btn btn-secondary" type="submit" id="button_submit">
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
        function cek_date() {
            if ($('#start_date').val() > $('#end_date').val()) {
                $(`#start_date`).addClass('is-invalid')
                $(`#end_date`).addClass('is-invalid')
                $('#button_submit').prop('disabled', true)
            } else {
                $(`#start_date`).removeClass('is-invalid')
                $(`#end_date`).removeClass('is-invalid')
                $('#button_submit').prop('disabled', false)
            }
        }

        document.getElementById('selectCompany').addEventListener('change', function() {
            const inputEventName = document.getElementById('inputEventName');
            const selectedOptions = this.selectedOptions;
            if (selectedOptions.length > 0) {
                let companyName = selectedOptions[0].text.replace(/^PT\.?\s*/i, '').trim();

                // Jika perusahaan adalah Semen Indonesia
                if (companyName === 'Semen Indonesia,Tbk' || companyName === 'Semen Indonesia (BUOP Tuban)') {
                    inputEventName.value = 'SIG INNOVATION AWARD';
                } else if (companyName === 'Semen Indonesia Logistik') { // Tambahkan kondisi untuk SILOG
                    inputEventName.value = 'SILOG INNOVATION AWARD';
                } else {
                    let companyInitial = companyName.split(' ').map(word => word[0]).join(
                    ''); // Ambil huruf pertama
                    inputEventName.value = `${companyInitial} INNOVATION AWARD`; // Setel nama event
                }
            }
        });
    </script>
@endpush
