@extends('layouts.app')
@section('title', 'Register Tim')
@push('css')
    {{-- <link href="{{ asset('template/dist/css/styles.css') }}" rel="stylesheet" /> --}}
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    {{-- <link href="{{ asset('../css/register.css') }}" rel="stylesheet" /> --}}
    <style type="text/css">
        .active-link {
            color: #ffc004;
            background-color: #e81500;
        }
    </style>
@endpush
@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Formulir Registrasi
                        </h1>
                    </div>
                    <div class="col-auto mb-3">
                        <a class="btn btn-outline-primary btn-sm" href="{{ route('paper.index') }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-xl px-4">
        @include('auth.user.paper.navbar')
        @if (isset($errors) && count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    {{ session('errors') }}
                </ul>
            </div>
        @endif
        <div class="row justify-content-center">
            <!-- Join Organization-->
            <div class="col-md-12">
                <div class="card border-0 shadow-lg mt-5 mb-5">
                    <div class="card-header text-center">
                        <div class="h3 text-primary mb-0">Formulir Pendaftaran Tim Inovasi SIG</div>
                    </div>
                    <div class="card-body p-5">
                        <form action="{{ route('paper.register.store') }}" method="POST" id="register-form"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <h6 class="small mb-1" for="inputTeamName">Nama Tim</h6>
                                <input class="form-control" name="team_name" value="{{ old('team_name') }}"
                                    id="inputTeamName" type="text" placeholder="Masukkan Nama Tim Anda" required />
                                @error('team_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1" for="inputInnovationTitle">Judul Inovasi</h6>
                                <input class="form-control" name="innovation_title" value="{{ old('innovation_title') }}"
                                    id="inputInnovationTitle" type="text" placeholder="Masukkan Judul Inovasi Tim Anda"
                                    required />
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1" for="inputLokasiInvovasi">Lokasi Implementasi Inovasi</h6>
                                <input class="form-control" name="inovasi_lokasi" value="{{ old('inovasi_lokasi') }}"
                                    id="inovasi_lokasi" type="text"
                                    placeholder="Masukkan Lokasi Implementasi Inovasi Tim Anda" required />
                            </div>
                            <!-- class="form-select form-control-solid" -->
                            <div class="mb-4">
                                <h6 class="small mb-1">Kategori</h6>
                                <select class="form-select" aria-label="Default select example" name="category"
                                    id="id_category" value="{{ old('category') }}" placeholder="Pilih Kategori Inovasi Anda"
                                    required onchange="addRow(this)">
                                    <!-- <option selected disabled>Select a category :</option>
                                                                                            @foreach ($datas_category as $row)
    <option value="{{ $row->id }}">{{ $row->category_name }}</option>
    @endforeach -->
                                </select>
                            </div>


                            <input type="hidden" name="status_lomba" value="AP">
                            <div class="mb-4">
                                <h6 class="small mb-1">Metodologi Makalah</h6>
                                <select class="form-select" name="metodologi_paper_id" id="id_metodologi_paper" required">
                                    <!-- Select2 akan mengisi opsi ini melalui AJAX -->
                                </select>
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1">Ketua Tim</h6>
                                <select class="form-select" aria-label="Default select example" name="leader"
                                    id="id_leader" value="{{ old('leader') }}" placeholder="Pilih Ketua Tim"
                                    onChange="show_identity(this); check_select(this);" required>
                                    <!-- <option selected disabled>Select a leader :</option> -->
                                    <!-- @foreach ($datas_user as $r_fasil)
    <option value="{{ $r_fasil->id }}">{{ $r_fasil->employee_id }} - {{ $r_fasil->name }}</option>
    @endforeach    -->
                                </select>
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1">Fasilitator</h6>
                                <select class="form-select" aria-label="Default select example" name="fasil"
                                    id="id_fasil" value="{{ old('fasil') }}" placeholder="Pilih Fasilitator"
                                    onChange="check_select(this)" required>

                                </select>

                            </div>
                            <div class="mb-4" id="anggota">

                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1">Unit</h6>
                                <input class="form-control form-control-solid" name="" id="unit"
                                    type="text" value="{{ old('') }}" readonly required />
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1">Departemen</h6>
                                <input class="form-control form-control-solid" name="" id="department"
                                    type="text" value="{{ old('') }}" readonly required />
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1">Direktorat</h6>
                                <input class="form-control form-control-solid" name="" id="directorate"
                                    type="text" value="{{ old('') }}" readonly required />
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1">Perusahaan</h6>
                                <input class="form-control form-control-solid" name="company" id="company"
                                    type="text" value="{{ old('company') }}" readonly required />
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1" for="inputHP">Nomor Telepon (WA)</h6>
                                <input class="form-control" name="phone_number" id="inputHP" type="text"
                                    value="{{ old('hp') }}" placeholder="Masukkan Nomor Telepon Anda" required />
                            </div>
                            <div class="mb-3">
                                <h6 class="small mb-1">Tema</h6>
                                <select class="form-select" aria-label="Default select example" name="theme"
                                    id="id_theme" value="{{ old('theme') }}" required>
                                    <!-- <option selected disabled>Select a theme :</option>
                                                            @foreach ($datas_theme as $row)
    <option value="{{ $row->id }}">{{ $row->theme_name }}</option>
    @endforeach -->
                                </select>
                            </div>
                            <div class="mb-4">
                                <h6 class="small mb-1">Status Inovasi</h6>
                                <select class="form-control" aria-label="Default select example" name="status_inovasi"
                                    id="chooseStatusInovasi" value="{{ old('status_inovasi') }}"
                                    placeholder="Pilih Status Inovasi Anda" required>
                                    <option value="Not Implemented">Not Implemented</option>
                                    <option value="Progress">Progress</option>
                                    <option value="Implemented">Implemented</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <h6 class="small mb-1">Abstrak</h6>
                                <textarea name="abstract" id="" cols="30" rows="5" class="form-control"></textarea>
                            </div>

                            <div class="mb-3">
                                <h6 class="small mb-1">Masalah</h6>
                                <textarea name="problem" id="" cols="30" rows="5" class="form-control"></textarea>
                            </div>

                            {{-- <div class="mb-3">
                                <h6 class="small mb-1">Dampak Masalah</h6>
                                <textarea name="problem_impact" id="" cols="30" rows="5" class="form-control"></textarea>
                            </div> --}}

                            <div class="mb-3">
                                <h6 class="small mb-1">Penyebab Utama</h6>
                                <textarea name="main_cause" id="" cols="30" rows="5" class="form-control"></textarea>
                            </div>

                            <div class="mb-3">
                                <h6 class="small mb-1">Solusi</h6>
                                <textarea name="solution" id="" cols="30" rows="5" class="form-control"></textarea>
                            </div>

                            {{-- <div class="mb-3">
                                <h6 class="small mb-1">Dampak / Hasil Solusi</h6>
                                <textarea name="outcome" id="" cols="30" rows="5" class="form-control"></textarea>
                            </div> --}}

                            {{-- <div class="mb-3">
                                <h6 class="small mb-1">Dampak Positif</h6>
                                <textarea name="performance" id="" cols="30" rows="5" class="form-control"></textarea>
                            </div> --}}

                            <div class="mb-3">
                                <h6 class="small mb-1">Upload Foto Tim (Resmi)</h6>
                                <input type="file" name="proof_idea" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-3">
                                <h6 class="small mb-1">Upload Foto Produk Inovasi</h6>
                                <input type="file" name="innovation_photo" class="form-control" accept="image/*">
                            </div>
                            @if (App::environment('stage'))
                                <div class="mb-3">
                                    <h6 class="small mb-1">Team dibuat tanggal</h6>
                                    <input type="date" name="team_created_at" class="form-control" required>
                                </div>
                            @endif
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
        function change_to_outsource(elemen) {
            let divfield2 = elemen.parentNode.parentNode
            let divfield = divfield2.parentNode
            let anggota_ke = divfield2.querySelector('h6').innerHTML.split(' ')[1]
            divfield2.remove();

            const input_freetext = `
                <div class="row ">
                    <h6 class="small mb-1">Anggota ${anggota_ke} (Outsource)</h6>
                    <div class="col-9 ">
                        <input type="text" class="form-control form-control-solid" name="anggota_outsource[]" id="id_anggota_${anggota_ke}" placeholder="Masukan nama anggota outsource">
                    </div>
                    <div class="col-3  d-flex flex-column justify-content-center">
                        <button class="btn btn-warning" value="Organic" type="button" id="btnOrganic" onclick="change_to_organic(this)">Organic</button>
                    </div>
                </div>
                `;

            divfield.innerHTML += input_freetext;
            check_select(divfield.childNodes[1].querySelectorAll('div')[0].childNodes[1])
        }

        function change_to_organic(elemen) {
            let divfield2 = elemen.parentNode.parentNode
            let divfield = divfield2.parentNode
            let anggota_ke = divfield2.querySelector('h6').innerHTML.split(' ')[1]
            divfield2.remove();

            addSelect(divfield.id)
        }

        // fungsi yang digunakan untuk mengubah semua select field selain yang data karyawan
        // (theme, category, event)
        function select2s(element_id) {

            var nama = element_id.split("_")[1];
            if (nama == 'category')
                var nama_table = 'categorie'
            else
                var nama_table = nama

            $("#" + element_id).select2({
                // theme: "classic",
                width: "100%",
                allowClear: true,
                placeholder: 'Pilih ' + nama.charAt(0).toUpperCase() + nama.slice(1) + " : ",
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET', // Metode HTTP POST
                    url: '{{ route('query.custom') }}',
                    dataType: 'json',
                    delay: 250, // Penundaan dalam milidetik sebelum permintaan AJAX dikirim
                    data: {
                        table: nama_table + "s",
                        where: {},
                        limit: 100
                    },
                    processResults: function(data) {
                        // Memformat data yang diterima untuk format yang sesuai dengan Select2
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item[nama +
                                        "_name"], // Nama yang akan ditampilkan di kotak seleksi
                                    id: item.id // Nilai yang akan dikirimkan saat opsi dipilih
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }

        // fungsi yang dijalankan ketika dom sudah terload
        // akan menginisasi semua select field agar menggunakkan library select2
        document.addEventListener("DOMContentLoaded", function() {
            select2s('id_category');
            select2s('id_theme');
            select2s('id_metodologi_paper');
            search_select2('id_leader');
            search_facilitator('id_fasil');
        });

        // fungsi untuk menampilkan dan mengset input value company beradasarkan leader
        function show_identity(element) {
    var leader_value = element.value;
    var companyField = document.getElementById("company");
    var unitField = document.getElementById("unit");
    var departmentField = document.getElementById("department");
    var directorateField = document.getElementById("directorate");
    console.log(leader_value)

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: '{{ route('getUsersWithCompany') }}',
        dataType: 'json',
        data: {
            employee_id: leader_value
        },
        success: function(response) {
            if (response.success) {
                console.log(response.data)
                companyField.value = response.data.co_name
                unitField.value = response.data.unit_name
                departmentField.value = response.data.department_name
                directorateField.value = response.data.directorate_name
            } else {
                console.error(response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}

        function addRow(element) {
            var anggota_array = document.querySelectorAll('select[name="anggota[]"]');
            var anggota_selectField_count = anggota_array.length;
            jumlah = 0;
            $('#id_metodologi_paper').on('select2:select', function(e) {
                var selectedData = e.params.data;
                console.log('Selected Max User:', selectedData.max_user);
                jumlah = selectedData.max_user;
            });
            if (anggota_selectField_count < jumlah)
                addInputRow(jumlah - anggota_selectField_count, anggota_selectField_count)
            else if (anggota_selectField_count > jumlah)
                removeInputRow(anggota_selectField_count - jumlah, anggota_selectField_count)
        }

        // fungsi mengurangi row select field
        function removeInputRow(jumlah, jumlah_yg_ada) {
    console.log("Jumlah yang dihapus:", jumlah, "Jumlah yang ada:", jumlah_yg_ada);

    // Hapus elemen dari indeks terakhir ke awal
    for (var i = jumlah_yg_ada; i > jumlah_yg_ada - jumlah; i--) {
        var div_input_row = document.getElementById(`id_anggota_row_${i}`);
        if (div_input_row) {
            div_input_row.remove();
        } else {
            console.error(`Elemen dengan ID id_anggota_row_${i} tidak ditemukan.`);
        }
    }
}
        // fungsi menambah row select field
        function addInputRow(jumlah, jumlah_yg_ada) {
            for (var i = jumlah_yg_ada; i < jumlah + jumlah_yg_ada; i++) {
                const div_input_anggota_field = `
                    <div class="mb-3" id="id_anggota_row_${(i + 1)}">

                    </div>`;

                document.getElementById('anggota').innerHTML += div_input_anggota_field;

                addSelect(`id_anggota_row_${(i + 1)}`);

            }
        }

        async function addSelect(id) {

            anggota_ke = id.split("_")[3]

            const select_anggota_field = `
                <div class="row ">
                                                <h6 class="small">Anggota ${anggota_ke}</h6>
                                                <div class="col-9 ">
                                                    <select name="anggota[]" class="form-select" id="id_anggota_${anggota_ke}" onChange="check_select(this)" ></select>
                                                </div>
                                                <div class="col-3  d-flex flex-column justify-content-center align-items-center">
                                                    <button id="btnOutsourcing" type="button" class="btn btn-primary btn-sm" style="width:100%" onclick="change_to_outsource(this)">Outsourcing</button>
                                                </div>
                                            </div>
                `;

            document.getElementById(id).innerHTML += select_anggota_field;
            await new Promise(resolve => setTimeout(resolve, anggota_ke * 1));

            anggota_ke = id.split("_")[3]
            search_select2(`id_anggota_${anggota_ke}`);

            // alert($(`#id_anggota_${anggota_ke}`).attr('id'))

        }

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
        // fungsi select2 untuk opsi yang membutuhkan data karyawan (fasilitator, leader, anggota)
        function search_facilitator(select_element_id) {

            $('#' + select_element_id).select2({
                allowClear: true,
                width: "100%",
                placeholder: "Pilih " + select_element_id.split("_")[1] + (select_element_id.split("_")[2] ? " " +
                    select_element_id.split("_")[2] + " : " : " : "),
                ajax: {
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET', // Metode HTTP POST
                    url: '{{ route('query.get_fasilitator') }}',
                    dataType: 'json',
                    delay: 250, // Penundaan dalam milidetik sebelum permintaan AJAX dikirim
                    data: function(params) {
                        // Data yang akan dikirim dalam permintaan POST
                        return {
                            unit: $("#unit").val(),
                            department: $("#department").val(),
                            directorate: $("#directorate").val(),
                            query: params.term // Menggunakan nilai input "query" sebagai parameter
                        };
                    },
                    processResults: function(data) {
                        // Memformat data yang diterima untuk format yang sesuai dengan Select2
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.employee_id + ' - ' + item.name + ' - ' + item
                                        .job_level, // Nama yang akan ditampilkan di kotak seleksi
                                    id: item.employee_id // Nilai yang akan dikirimkan saat opsi dipilih
                                };
                            })
                        };
                    },
                    cache: true
                }
            });
        }
        // mengecek setiap select agar tidak terjadi karyawan yang terpilih 2 kali
        function check_select(element) {
            // fungsi untuk mengecek apakah ada karyawan yang dipilih yang sama
            var anggota_id = element.id;
            var anggota_ke = anggota_id // .split("_")[2];
            var anggota = Array.from(document.getElementsByName("anggota[]"));
            anggota.push(document.getElementById("id_leader"));
            anggota.push(document.getElementById("id_fasil"));

            var flag = 0;
            var flag2 = 0;
            //cek dengan nested loop
            anggota.forEach(function(input) {
                var nilai = input.value;
                // console.log(element.id + " " + input.id)
                var invalid_anggota = document.getElementById("invalid_anggota_" + input.id);
                var feedback_anggota = document.getElementById("feedback_anggota_" + input.id);
                if (element.value != "" && element.value == nilai && anggota_ke != input.id) {
                    // jika element yang dipilih memiliki nilai yang sama dengan id field lainnya, maka
                    // ubah flag menjadi 1
                    flag = 1;
                } else if (invalid_anggota) {
                    // mengecek element yang sudah ada is-invalid, apakah masih ada yang menyamai valuenya
                    anggota.forEach(function(input2) {
                        if (input.value == input2.value && input.id != input2.id) {
                            // ini flag2 untuk input yang sudah ada is-invalid
                            flag2 = 1;
                        }
                    })
                    if (!flag2) {
                        invalid_anggota.remove();
                        feedback_anggota.remove();
                    }
                }
            })

            var invalid_anggota = document.getElementById("invalid_anggota_" + anggota_ke);
            var feedback_anggota = document.getElementById("feedback_anggota_" + anggota_ke);
            if (flag && !invalid_anggota) {
                var selectNode = element.parentNode
                const divfield_invalid = document.createElement('div');
                divfield_invalid.className = "is-invalid";
                divfield_invalid.id = "invalid_anggota_" + anggota_ke;
                // divfield_invalid.innerText = "masukk";
                selectNode.appendChild(divfield_invalid);

                const divfield_feedback = document.createElement('div');
                divfield_feedback.className = "invalid-feedback";
                divfield_feedback.id = "feedback_anggota_" + anggota_ke;
                divfield_feedback.innerText = "Anggota sudah dipilih, Mohon pilih anggota yang lain.";
                selectNode.appendChild(divfield_feedback);
                document.getElementById("button_submit").setAttribute('disabled', 'true');
            }
            if (!flag && invalid_anggota) {
                invalid_anggota.remove();
                feedback_anggota.remove();
            }

            if (!flag && !flag2) {
                document.getElementById("button_submit").removeAttribute('disabled');
            }
        }

        function loading(element) {
            var id = element.id
            element.setAttribute('disabled', 'true');
            element.innerText = "Process...";
        }

        document.getElementById("register-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Mencegah pengiriman langsung
            var id_button = document.getElementById("button_submit")
            id_button.setAttribute('disabled', 'true');;
            id_button.innerText = "Process...";

            setTimeout(function() {
                document.getElementById("register-form").submit();
            }, 1000);
        });

        // function changeStatusLomba(elemen){
        //     document.getElementById('status_lomba').value = (elemen.value == 8) ? "group" : "AP"
        // }
        $(document).ready(function() {
            $('#id_metodologi_paper').select2({
                width: '100%',
                placeholder: 'Pilih Metodologi Paper',
                ajax: {
                    url: '{{ route('query.metodologi_papers') }}',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.id,
                                    text: item.name,
                                    max_user: item.max_user
                                };
                            })
                        };
                    },
                    cache: true
                }
            });

            // Event listener untuk mengatur jumlah form anggota
            $('#id_metodologi_paper').on('select2:select', function(e) {
                var selectedData = e.params.data;
                var maxUser = selectedData.max_user;

                console.log('Selected Max User:', maxUser);

                // Hitung jumlah elemen anggota yang ada
                var anggotaArray = document.querySelectorAll('select[name="anggota[]"]');
                var anggotaCount = anggotaArray.length;
                console.log('Current Anggota Count:', anggotaCount);

                // Tambah atau hapus form anggota sesuai kebutuhan
                if (anggotaCount < maxUser) {
                    addInputRow(maxUser - anggotaCount, anggotaCount);
                } else if (anggotaCount > maxUser) {
                    removeInputRow(Math.abs(maxUser - anggotaCount), anggotaCount);

                }

            });
        });
    </script>
@endpush
