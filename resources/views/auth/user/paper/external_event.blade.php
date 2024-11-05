@extends('layouts.app')
@section('title', 'Data Assessment')
@push('css')
@endpush
@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            External Event
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="mb-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <form action="{{ route('paper.register.external') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <div class="row">
                <div class="col-xl-6">
                    <!-- Profile picture card-->
                    <div class="card mb-4">
                        <div class="card-header">Team Information</div>
                        <div class="card-body">
                            <!-- Form Row-->
                            <div class="mb-2">
                                <!-- Form Group (choose event)-->
                                <label class="small mb-1" for="inputTeamName">Team Name</label>
                                <input type="text" name="team_name" id="inputTeamName" class="form-control"
                                    value="{{ $dt_team->team_name }}" readonly>
                            </div>
                            @error('team_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <div class="mb-2">
                                <!-- Form Group (choose event)-->
                                <label class="small mb-1" for="inputInnovationTitle">Innovation Title</label>
                                <input type="text" name="innovation_title" id="inputInnovationTitle" class="form-control"
                                    value="{{ $dt_team->innovation_title }}">
                            </div>
                            <div class="mb-2" id="div_fasilitator">
                                <label class="small mb-1" for="id_fasil">Fasilitator</label>

                                <select class="form-select" aria-label="Default select example" name="fasilitator"
                                    id="id_fasil" onChange="check_select(this)" required></select>
                            </div>
                            <div class="mb-2" id="div_leader">
                                <label class="small mb-1" for="id_leader">Leader</label>

                                <select class="form-select" aria-label="Default select example" name="leader"
                                    id="id_leader" onChange="check_select(this)" required></select>
                            </div>
                            <div>
                                <div class="mb-2" id="id_anggota_row_1">
                                    <div class="row">
                                        <label class="small mb-1" for="id_anggota_1">Anggota 1</label>
                                        <div class="col-9">
                                            <select class="form-select" aria-label="Default select example" name="anggota[]"
                                                id="id_anggota_1" onChange="check_select(this)"></select>
                                        </div>
                                        <div class="col-3  d-flex flex-column justify-content-center align-items-center">
                                            <button id="btnOutsourcing" type="button" class="btn btn-primary btn-sm"
                                                style="width:100%" onclick="change_to_outsource(this)">Outsourcing</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2" id="id_anggota_row_2">
                                    <div class="row">
                                        <label class="small mb-1" for="id_anggota_2">Anggota 2</label>
                                        <div class="col-9">
                                            <select class="form-select" aria-label="Default select example" name="anggota[]"
                                                id="id_anggota_2" onChange="check_select(this)"></select>
                                        </div>
                                        <div class="col-3  d-flex flex-column justify-content-center align-items-center">
                                            <button id="btnOutsourcing" type="button" class="btn btn-primary btn-sm"
                                                style="width:100%" onclick="change_to_outsource(this)">Outsourcing</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2" id="id_anggota_row_3">
                                    <div class="row">
                                        <label class="small mb-1" for="id_anggota_3">Anggota 3</label>
                                        <div class="col-9">
                                            <select class="form-select" aria-label="Default select example"
                                                name="anggota[]" id="id_anggota_3"
                                                onChange="check_select(this)"></select>
                                        </div>
                                        <div class="col-3  d-flex flex-column justify-content-center align-items-center">
                                            <button id="btnOutsourcing" type="button" class="btn btn-primary btn-sm"
                                                style="width:100%"
                                                onclick="change_to_outsource(this)">Outsourcing</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2" id="id_anggota_row_4">
                                    <div class="row">
                                        <label class="small mb-1" for="id_anggota_4">Anggota 4</label>
                                        <div class="col-9">
                                            <select class="form-select" aria-label="Default select example"
                                                name="anggota[]" id="id_anggota_4"
                                                onChange="check_select(this)"></select>
                                        </div>
                                        <div class="col-3  d-flex flex-column justify-content-center align-items-center">
                                            <button id="btnOutsourcing" type="button" class="btn btn-primary btn-sm"
                                                style="width:100%"
                                                onclick="change_to_outsource(this)">Outsourcing</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <!-- Form Group (choose event)-->
                                <label class="small mb-1" for="inputCompany">Company</label>
                                <select name="company" id="inputCompany" class="form-control">

                                    @foreach ($datas_company as $opt)
                                        <option value="{{ $opt->company_code }}"
                                            {{ $dt_team->code_company === $opt->company_code ? 'selected' : '' }}>
                                            {{ $opt->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <!-- Form Group (choose event)-->
                                <label class="small mb-1" for="phone_number">Nomor Telepon</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control"
                                    value="{{ $dt_team->phone_number }}">
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <!-- Account details card-->
                    <div class="card mb-4">
                        <div class="card-header">Form Event External</div>
                        <div class="card-body">
                            <!-- Form Row-->
                            <div class="mb-3">
                                <!-- Form Group (choose event)-->
                                <label class="small mb-1" for="inputEvent">Event</label>
                                <select name="status_lomba" class="form-select" id="inputEvent">
                                    <option value="national">National</option>
                                    <option value="international">International</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1" for="inputPaper">Upload Paper</label>
                                <input type="file" class="form-control" name="file_paper" id="inputPaper"
                                    accept=" .doc, .docx, .pdf">
                            </div>
                            @error('file_paper')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <!-- Form Group (upload PPT)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="inputPPT">Upload PDF</label>
                                <input type="file" class="form-control" name="ppt" id="inputPPT"
                                    accept=" .mp4, .mov, .avi">
                            </div>
                            @error('ppt')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <!-- Form Group (Upload Video)-->
                            <div class="mb-4">
                                <label class="small mb-1" for="inputVideo">Upload Video</label>
                                <input type="file" class="form-control" name="video" id="inputVideo"
                                    accept=" .ppt, .pptx, .pdf">
                            </div>
                            @error('video')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            <!-- Submit button-->
                            <div class="d-grid">
                                <button class="btn btn-primary" type="submit" id="button_submit">Submit</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection


@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <!-- Contoh menggunakan CDN -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
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
                // console.log(input);
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

        function change_to_outsource(elemen) {
            console.log(elemen);
            let divfield2 = elemen.parentNode.parentNode
            let divfield = divfield2.parentNode
            let anggota_ke = divfield2.querySelector('label').innerHTML.split(' ')[1]
            divfield2.remove();

            const input_freetext = `
        <div class="row ">
            <label class="small mb-1">Anggota ${anggota_ke} (Outsource)</label>
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
            let anggota_ke = divfield2.querySelector('label').innerHTML.split(' ')[1]
            divfield2.remove();

            addSelect(divfield.id)
        }

        async function addSelect(id) {

            anggota_ke = id.split("_")[3]

            const select_anggota_field = `
        <div class="row ">
            <label class="small">Anggota ${anggota_ke}</label>
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
            search_select2(`id_anggota_${anggota_ke}`, '', '');

            // alert($(`#id_anggota_${anggota_ke}`).attr('id'))

        }

        // fungsi select2 untuk opsi yang membutuhkan data karyawan (fasilitator, leader, anggota)
        function search_select2(select_element_id, value, name) {

            $('#' + select_element_id).select2({
                // allowClear: true,
                // theme: "classic",
                allowClear: true,
                width: "10    0%",
                async: false,
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
            if (value != '')
                $('#' + select_element_id).append('<option value="' + value + '">' + value + ' - ' + name + '</option>');
        }

        document.addEventListener("DOMContentLoaded", function() {

            var data_member = {{ Js::from($data_member) }}

            member_ke = 0
            data_member.forEach(function(member) {
                if (member.status == 'outsource') {
                    member_ke++
                    change_to_outsource(document.getElementById(`id_anggota_${member_ke}`))
                    document.getElementById(`id_anggota_${member_ke}`).value = (me mber.name)
                } else if (member.status == 'leader') {
                    search_select2('id_leader', member.employee_id,
                        `${member.employee_id} - ${member.name}`)
                } else if (member.status == 'facilitator') {
                    search_select2('id_fasil', member.employee_id, `${member.employee_id} - ${member.name}`)
                } else {
                    member_ke++
                    search_select2(`id_anggota_${member_ke}`, member.employee_id,
                        `${member.employee_id} - ${member.name}`)
                }
            });

            for (let i = member_ke + 1; i <= 4; i++) {
                search_select2(`id_anggota_${i}`, '', '')
            }

        });
    </script>
@endpush
