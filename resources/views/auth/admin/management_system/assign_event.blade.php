@extends('layouts.app')
@section('title', 'Assign Event')
@push('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
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
            <!-- Modul Form Add Event!!-->
            <div class="container-xl px-4 mt-4">
                <div class="row justify-content-center">
                    <div class="mb-3">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                                <i data-feather="check-circle"></i> {{ session('success') }}
                                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        @if (session('errors'))
                            <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                                <i data-feather="alert-circle"></i> {{ session('errors') }}
                                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                    </div>
                    <div class="col-xl-7 col-lg-8 col-md-9 col-sm-11">
                        <div class="card border-0 shadow-sm mb-5">
                            <div class="card-header text-center" style="background-color: rgba(0, 123, 255, 0.1);">
                                <h4 class="mb-0">Form Add Event</h4>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('management-system.assign.event.store') }}" method="POST" id="assign-juri-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label" for="selectCompany">Company</label>
                                        <select id="selectCompany" class="form-select" name="company_code[]" required>
                                            <option value="" selected disabled>Select a company</option>
                                            @foreach ($datas_company as $cp)
                                                <option value="{{ $cp->company_code }}">{{ $cp->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="inputEventName">Event Name</label>
                                        <input class="form-control" id="inputEventName" type="text" name="event_name"
                                            placeholder="Masukkan Nama Event" value="{{ old('event_name') }}" required>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="start_date">Start Date</label>
                                            <input class="form-control" type="date" id="start_date" name="start_date" onchange="cek_date()" required>
                                            <div class="invalid-feedback">Tanggal mulai tidak boleh setelah tanggal berakhir.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="end_date">End Date</label>
                                            <input class="form-control" type="date" id="end_date" name="end_date" onchange="cek_date()" required>
                                            <div class="invalid-feedback">Tanggal berakhir tidak boleh sebelum tanggal mulai.</div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="year">Year</label>
                                        <select name="year" id="year" class="form-select" required>
                                            <option value="" selected disabled>Pilih Tahun</option>
                                            @foreach ($years as $year)
                                                <option value="{{ $year }}">{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="type">Event Type</label>
                                        <select class="form-select" id="type" name="type" required>
                                            <option value="" selected disabled>Pilih Tipe Event</option>
                                            <option value="AP">Anak Perusahaan</option>
                                            <option value="group">Group</option>
                                            <option value="national">National</option>
                                            <option value="international">International</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="data_description">Description</label>
                                        <textarea class="form-control" name="description" id="data_description" rows="5"
                                            placeholder="Masukkan deskripsi event..." required></textarea>
                                        @if ($errors->has('description'))
                                            <div class="text-danger">{{ $errors->first('description') }}</div>
                                        @endif
                                    </div>

                                    <div class="d-grid">
                                        <button class="btn btn-primary" type="submit" id="button_submit">
                                            <i data-feather="send"></i> Submit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
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
