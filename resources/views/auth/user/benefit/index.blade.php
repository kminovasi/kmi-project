<!-- resources/views/home.blade.php -->
@extends('layouts.app')
@section('title', 'Form Benefit')
@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .status-text.good {
            color: green;
        }

        .status-text.bad {
            color: red;
        }

        .counter-text {
            color: #666;
            font-size: 0.9em;
        }
    </style>
@endpush
@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white ">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Form Benefit
                        </h1>
                    </div>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="container-xl px-4">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> List Error:
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </header>
    <div id="layoutAuthentication_content">
        <main>
            <div class="container-xl px-4">
                <div class="row justify-content-center">
                    <div class="col-xl-9 col-lg-9 col-md-9 col-sm-11">
                        <div class="card border-0 mt-5 mb-5">
                            <div class="card-header">Form Benefit</div>
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('benefit.store.user', $row->paper_id) }}" method="POST"
                                enctype="multipart/form-data" id="assign-gm-form">
                                @csrf
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="mb-1" for="dataFinancial{{ $row->paper_id }}">Financial
                                            (Real) </label>

                                        <input class="form-control" id="dataFinancial{{ $row->paper_id }}" type="text"
                                            name="financial" value="{{ $row->getFinancialFormattedAttribute() }}"
                                            oninput="formatCurrency(this)" required {{ $is_disabled ? 'disabled' : '' }}>
                                    </div>
                                    <div class="mb-3">
                                        <label class="mb-1" for="dataBenefitPtential{{ $row->paper_id }}">Benefit
                                            Potential</label>
                                        <input class="form-control" id="dataBenefitPtential{{ $row->paper_id }}"
                                            type="text" name="potential_benefit"
                                            value="{{ $row->getPotentialBenefitFormattedAttribute() }}"
                                            oninput="formatCurrency(this)" {{ $is_disabled ? 'disabled' : '' }} required>
                                    </div>
                                    <div class="form-group">
                                        <h5>Non-Financial Benefit</h5>
                                        @foreach ($benefit_custom as $bc)
                                            <div class="mb-3">
                                                <label class="mb-1"
                                                    for="bencus-{{ $bc['id'] }}">{{ $bc['name_benefit'] }}</label>
                                                <textarea class="form-control character-counter" id="bencus-{{ $bc['id'] }}" name="bencus[{{ $bc['id'] }}]"
                                                    type="text" required {{ $is_disabled ? 'disabled' : '' }}>{{ $bc['value'] }}</textarea>
                                                <div class="mt-2 d-flex justify-content-between">
                                                    <span class="status-text"
                                                        id="status-bencus-{{ $bc['id'] }}"></span>
                                                    <span class="counter-text" id="counter-bencus-{{ $bc['id'] }}">0
                                                        karakter</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="mb-3">
                                        <label class="mb-1" for="potensiReplikasi{{ $row->paper_id }}">Potensial
                                            Replikasi</label>
                                        <select class="form-select" aria-label="Default select example"
                                            name="potensi_replikasi" id="choosePotensiReplikasi"
                                            value="{{ old('potensi_replikasi') }}" {{ $is_disabled ? 'disabled' : '' }}
                                            required {{ $is_owner ? '' : 'disabled' }}>
                                            <option value="Bisa Direplikasi">Bisa Direplikasi</option>
                                            <option value="Tidak Bisa Direplikasi">Tidak Bisa Direplikasi</option>
                                        </select>
                                    </div>


                                    <div class="mb-3">
                                        <h6 class="small mb-1">Pilih Band 1 (General Manager)</h6>
                                        <input type="hidden" name="team_id" value="{{ $row->team_id }}">
                                        <select class="form-select @error('gm_id') is-invalid @enderror"
                                            aria-label="Default select example" name="gm_id" id="id_gm"
                                            value="{{ old('gm_id') }}" placeholder="Pilih GM"
                                            {{ $is_disabled ? 'disabled' : '' }}></select>
                                        @error('gm_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @if ($gmName !== null)
                                        <div class="mb-3">
                                            <input type="hidden" name="oldGm" value="{{ $gmName->employee_id }}">
                                            <div class="h6">Nama General Manager yang di Pilih Sebelumnnya : </div>
                                            <div class="h5">{{ $gmName->name }}</div>
                                            <hr>
                                        </div>
                                    @endif
                                    <div class="mb-3" id="file_row_{{ $row->paper_id }}">
                                        <label class="mb-1" for="dataFileReview{{ $row->paper_id }}">Berita Acara
                                            Benefit (Pdf)</label>
                                        <input class="form-control" id="file_review_{{ $row->paper_id }}" type="file"
                                            name="file_review" accept=".pdf" oninput="check_file('{{ $row->paper_id }}')"
                                            {{ $is_disabled ? 'disabled' : '' }}>
                                        <!-- <input type="text" id="file_path_{{ $row->paper_id }}" value="{{ $row->file_review }}" hidden> -->
                                        <div class="is-invalid" id="invalid_file_{{ $row->paper_id }}"></div>
                                        <div class="invalid-feedback" id="feedback_file_{{ $row->paper_id }}"></div>
                                    </div>
                                    <hr>

                                    <div class="file-review mb-0">
                                        @if ($file_content)
                                            <!-- Tampilkan gambar jika ada file -->
                                            <embed src="{{ route('benefit.preview.benefit', ['paper_id' => $row->paper_id]) }}" width="100%" height="500px" type="application/pdf">
                                        @else
                                            <p>No File Attached</p>
                                        @endif
                                    </div>

                                </div>
                                <div class="card-footer">
                                    <div class="mb-0 d-flex justify-content-between">
                                        <a href="{{ route('paper.index') }}" class="btn btn-red">Tutup</a>
                                        <button class="btn btn-primary btn-end" type="submit"
                                            id="submit_benefit_{{ $row->paper_id }}">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </main>
    </div>

    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script type="">
        document.addEventListener("DOMContentLoaded", function() {
            var selectElements = document.querySelectorAll('select');
            selectElements.forEach(function(select) {

            });
            search_select2('id_gm')

            // Ambil semua textarea dengan class character-counter
    const textareas = document.querySelectorAll('.character-counter');

    textareas.forEach(textarea => {
        const id = textarea.id;
        const statusElement = document.getElementById('status-' + id);
        const counterElement = document.getElementById('counter-' + id);

        // Fungsi untuk update counter dan status
        function updateCount() {
            const length = textarea.value.length;
            counterElement.textContent = `${length} / 75 karakter`;

            if (length >= 75 && length !== 0) {
                statusElement.textContent = 'Isi Baik';
                statusElement.classList.remove('bad');
                statusElement.classList.add('good');
            } else {
                if(length !== 0){
                    statusElement.textContent = 'Isi Kurang Baik';
                    statusElement.classList.remove('good');
                    statusElement.classList.add('bad');
                }
            }
        }

        // Event listeners
        textarea.addEventListener('input', updateCount);
        textarea.addEventListener('change', updateCount);

        // Initial count
        updateCount();
    });
        });

        document.getElementById('choosePotensiReplikasi').addEventListener('change', function() {
            // Ambil nilai yang dipilih
            var selectedValue = this.value;
            // Ubah nilai pada elemen yang ditampilkan
            document.getElementById('potensiReplikasiValue').innerText = selectedValue;
        });
        //mengubah input supaya menjadi seperti 10.000
        function formatCurrency(input) {
            let value = parseFloat(input.value.replace(/[^\d]/g, ''));

            if (!isNaN(value)) {
                let formattedRupiah = value.toLocaleString('id-ID');
                input.value = formattedRupiah;
            } else {
                input.value = '';
            }
        }

        function check_file(id) {
            // alert(document.getElementById(`feedback_file_${id}`).className);
            file_input_type = document.getElementById(`file_review_${id}`).files[0].type
            feedback_file = document.getElementById(`feedback_file_${id}`)

            button_file = document.getElementById(`submit_benefit_${id}`)

            if (file_input_type !== 'application/pdf') {
                button_file.setAttribute('disabled', true);
                feedback_file.innerHTML = "Berita Acara Wajib diisi dalam bentuk PDF";
            } else {
                button_file.removeAttribute('disabled');
                feedback_file.innerHTML = "";
            }
        }

        // fungsi select2 untuk opsi yang membutuhkan data karyawan (fasilitator, leader, anggota)
        function search_select2(select_element_id) {

            $('#' + select_element_id).select2({
                // allowClear: true,
                // theme: "classic",
                allowClear: true,
                width: "100%",
                placeholder: "Pilih Band 1",
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST', // Metode HTTP POST
                    url: '{{ route('query.get_GM') }}',
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
                                        .company_name, 
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
        </script>
    @endpush

@endsection
