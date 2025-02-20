@extends('layouts.app')
@section('title', 'Data Assessment')
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css" rel="stylesheet">
<style>
    #textarea {
        width: 100%;
        height: auto;
    }

    .active-link {
        color: #ffc004;
        background-color: #e81500;
    }

    .display thead th,
    .display tbody td {
        border: 0.5px solid #ddd;
        /* Atur warna dan ketebalan garis sesuai kebutuhan */
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
                        <div class="page-header-icon"><i data-feather="file-text"></i></div>
                        Lihat Template Penilaian
                    </h1>

                </div>

            </div>
        </div>
    </div>
</header>
<!-- Main page content-->
<div class="container-xl px-4 mt-4">
    @if (auth()->check() && auth()->user()->role == 'Superadmin' ||  auth()->user()->role == 'Admin')
    <div class="p-2 border-bottom">
        <a href="{{ route('assessment.show.template') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.show.template') ? 'active-link' : '' }}">Template
            Penilaian</a>
        <a href="{{ route('assessment.show.point') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.show.point') ? 'active-link' : '' }}">Pengaturab Poin Penilaian</a>
    </div>
    @endif
    <div class="mb-3">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            {{ session('error') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Berhasil dikirim di event
                </div>
                <div class="card-body">
                    <div class="ms-2">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="small">Acara</div>
                                <div class="text-md text-muted" id="event_name"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="small">Tahun</div>
                                <div class="text-md text-muted" id="event_year"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="small">Perusahaan</div>
                                <div class="text-md text-muted" id="event_company"></div>
                            </div>
                        </div>
                    </div>
                    <hr />
                </div>
            </div>
        </div>
    </div>
    <div class="card mb-4 col-12">
        <div class="card-body">
            <button class="btn btn-outline-primary btn-sm" style="margin-right: 10px;" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
            <button id="select-all-button" class="btn btn-outline-primary btn-sm">Select All</button>
            <form id="formAssign" action="{{ route('assessment.update.status') }}" method="POST">
                @csrf
                @method('PUT')
                <!-- <input type="text" name="year" id="inputYear" hidden> -->
                <input type="text" name="category" id="inputCategory" hidden>
                <input type="text" name="event" id="inputEvent" hidden>
                <table id="datatable-assessment-point">
                </table>
                <hr>
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <h6 class="card-title text-primary mb-3 d-flex align-items-center">
                            <i class="me-2" data-feather="info"></i>
                            <span>Informasi</span>
                        </h6>
                        <div id="konfirmasiScore" class="bg-light p-3 rounded border">
                            <!-- Content for the information will go here -->
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-5 mb-3">
                        <input class="form-control" type="number" name="minimumscore_oda" id="minimumscore_oda" onInput="validasi_minimum_score(this, 900)" placeholder="Masukkan skor minimum On Desk Assessment" {{ session('buttonStatus') == 'disabled' ? 'disabled' : '' }}>
                        <div class="invalid-feedback">
                            skor minimum tidak boleh melebihi batas.
                        </div>
                    </div>
                    <div class="col-md-5 mb-3">
                        <input class="form-control" type="number" name="minimumscore_pa" id="minimumscore_pa" onInput="validasi_minimum_score(this, 950)" placeholder="Masukkan skor minimum presentasi" {{ session('buttonStatus') == 'disabled' ? 'disabled' : '' }}>
                        <div class="invalid-feedback">
                            skor minimum tidak boleh melebihi batas.
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="btnAssign" {{ session('buttonStatus') == 'disabled' ? 'disabled' : '' }}>Kirim</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- modal untuk update template --}}
<div class="modal fade" id="updatePoint" tabindex="-1" role="dialog" aria-labelledby="updateTemplatePoint" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateTemplatePoint">Perbarui Poin Penilaian</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updatePointAssessment" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body px-4 py-3">
                    <!-- Point Assessment -->
                    <div class="mb-3">
                        <label class="mb-1 fw-bold text-muted" for="inputPoint">Poin Penilaian</label>
                        <input type="text" class="form-control shadow-sm" name="point" id="inputPoint" value="" readonly>
                    </div>

                    <!-- Detail Point -->
                    <div class="mb-3">
                        <label class="small mb-1 fw-bold text-muted" for="inputDetailPoint">Detail</label>
                        <textarea name="detail_point" id="inputDetailPoint" cols="10" rows="5" class="form-control shadow-sm"></textarea>
                    </div>

                    <!-- Max Score -->
                    <div class="mb-3">
                        <label class="mb-1 fw-bold text-muted" for="inputScoreMax">Poin Maksimal</label>
                        <input type="number" class="form-control shadow-sm" name="score_max" id="inputScoreMax" value="" min="0" step="1" required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Modal Untuk Delete Template Assessment --}}
{{-- <div class="modal fade" id="deletePoint" tabindex="-1" role="dialog" aria-labelledby="deletePointTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="deletePointAssessment" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePointTitle">Konfirmasi Hapus Data</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah yakin data ini akan dihapus ?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-danger" type="submit">Delete</button>
                </div>

            </div>
        </form>
    </div>
</div> --}}

{{-- modal filter Assessment Point Setting --}}
<div class="modal fade" id="filterModal" role="dialog" aria-labelledby="detailTeamMemberTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="detailTeamMemberTitle">
                    <i class="me-2" data-feather="filter"></i> Filter Options
                </h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <!-- Body -->
            <div class="modal-body px-4 py-3">
                <!-- Company Filter -->
                <div class="mb-4">
                    <label class="mb-1 fw-bold text-muted" for="filter-category">Company</label>
                    <select id="filter-category" name="filter-category" class="form-select shadow-sm">
                        <option value="BI/II">Implemented</option>
                        <option value="IDEA">IDEA Box</option>
                    </select>
                </div>
                <!-- Event Filter -->
                <div class="mb-4 {{ Auth::user()->role == 'Admin' ? 'd-none' : '' }}">
                    <label class="mb-1 fw-bold text-muted" for="filter-event">Event</label>
                    <select id="filter-event" name="filter-event" class="form-select shadow-sm">
                        @foreach ($data_event as $event)
                        <option value="{{ $event->id }}">{{ $event->event_name }} - {{ $event->year }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <!-- Footer -->
            <div class="modal-footer bg-light d-flex justify-content-end">
                <button class="btn btn-danger" type="button" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')
<script src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
</script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="">
    $(document).ready(function() {

        get_data_min_score($('#filter-event').val(), $('#filter-category').val())
        get_data_event($('#filter-event').val())

        document.getElementById('inputCategory').value = $('#filter-category').val()
        document.getElementById('inputEvent').value = $('#filter-event').val()
        // document.getElementById('inputYear').value = $('#filter-year').val()

        dataTable = $('#datatable-assessment-point').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('query.get.point.assessment') }}",
                "type": "GET",
                "async": false,
                "dataSrc": function (data) {
                    return data.data;
                },
                data: function (d) {
                    d.filterCategory = $('#filter-category').val();
                    // d.filterYear = $('#filter-year').val();
                    d.filterEvent = $('#filter-event').val();
                }
            },
            "columns": [
                {"data": "DT_RowIndex", "title": "No"},
                {"data": "point", "title": "Point Assessment"},
                {"data": "detail_point", "title": "Detail Point Assessment"},
                {"data": "pdca", "title": "Category"},
                {"data": "score_max", "title": "Score Max"},
                {"data": "assign","title": "Assign"
                //     "render": function (data, type, row) {
                //     // Check if checkbox is already checked on initial load
                //     // let isChecked = data ? 'checked' : '';
                //     let isChecked = '';
                //     return `<input type="checkbox" name="assessment_poin_id[]" value="${row.id}" ${isChecked}>`;
                // }
                },
                {"data": "status_point", "title": "Status"},
                {"data": "action", "title": "Action"},
                {"data": "stage", "title": "Stage"}
            ],

            "scrollY": true,
            "paging": false,
            "searching" : false,
            "ordering" : false,

        });

        var totalMaxScore = 0;
        cek(); // Call cek() to calculate initial score

        $('#filter-event').on('change', function () {
            dataTable.ajax.reload();
            cek()
            document.getElementById('inputEvent').value = $('#filter-event').val()
            get_data_min_score($('#filter-event').val(),  $('#filter-category').val())
            get_data_event($('#filter-event').val())
        });

        $('#filter-category').on('change', function () {
            dataTable.ajax.reload();
            cek()
            document.getElementById('inputCategory').value = $('#filter-category').val()
            get_data_min_score($('#filter-event').val(),  $('#filter-category').val())
        });

        $('#datatable-assessment-point tbody input[type="checkbox"]').on('change', function() {
            cek();
        });

        // Inisialisasi 'selectAllButton' dan set 'active' menjadi false
        const selectAllButton = $('#select-all-button');
        selectAllButton.removeClass('active');

        $('#select-all-button').on('click', function () {
        // Get all checkboxes in the 'assign' column
        const checkboxes_assign = $('#datatable-assessment-point tbody input[type="checkbox"]');

        // Toggle their checked state based on the current "Select All" button state
        if ($(this).hasClass('active')) {
            checkboxes_assign.prop('checked', false);
            $(this).removeClass('active');
        } else {
            checkboxes_assign.prop('checked', true);
            $(this).addClass('active');
        }

        // Call cek() function to update total score
        cek();
    });
    });

        // $('#filter-year').on('change', function () {
        //     dataTable.ajax.reload();
        //     cek()
        //     document.getElementById('inputYear').value = $('#filter-year').val()
        //     get_data_min_score($('#filter-event').val(), $('#filter-year').val(), $('#filter-category').val())
        // });

    //     cek()
    // });
    function get_data_point(assessment_point_id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',

            url: '{{ route('query.customassesment') }}',
            data: {
                table: "pvt_assessment_events",
                where: {
                    "id": assessment_point_id
                },
                limit: 1
            },
            // dataType: 'json',
            success: function(response) {
                document.getElementById("inputPoint").value = response[0].point;
                document.getElementById("inputScoreMax").value = response[0].score_max;
                //document.getElementById("inputDetailPoint").value = response[0].detail_point;

                var inputDetailPoint = document.getElementById("inputDetailPoint"); //agar detail bisa ditambah tapi tdk dapat dihapus
                inputDetailPoint.defaultValue = response[0].detail_point;
                inputDetailPoint.value = inputDetailPoint.defaultValue;

                var isDeleting = false;
                inputDetailPoint.addEventListener("keydown", function(event) {
                    if (event.key === "Backspace" || event.key === "Delete") {
                        isDeleting = true;
                    }
                });

                inputDetailPoint.addEventListener("input", function(event) {
                    if (isDeleting && event.target.value.length < inputDetailPoint.defaultValue.length) {
                        event.target.value = inputDetailPoint.defaultValue;
                        isDeleting = false;
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        //link untuk update
        var form = document.getElementById('updatePointAssessment');
        var url = `{{ route('assessment.update.point', ['id' => ':assessment_point_id']) }}`;
        url = url.replace(':assessment_point_id', assessment_point_id);
        form.action = url;
    }

    function delete_point(assessment_point_id) {
        //link untuk update
        var form = document.getElementById('deletePointAssessment');
        var url = `{{ route('assessment.delete.point', ['id' => ':assessment_point_id']) }}`;
        url = url.replace(':assessment_point_id', assessment_point_id);
        form.action = url;
    }

    function cek() {
        totalMaxScore = hitungTotalMaxScore()
        var selectElement = document.getElementById("filter-category");
        var selectedValue = selectElement.value;

        if (selectedValue == "BI/II") {
    // Set nilai antara 920 hingga 1000
    nilai = Math.min(1000, Math.max(920, totalMaxScore));
} else {
    nilai = 100; // Untuk kasus selain "BI/II"
}

        sisaMaxScore = nilai - totalMaxScore

        if(totalMaxScore == nilai){
            document.getElementById("konfirmasiScore").innerHTML = `Total Score Max <b class="text-green" id="totalScore"> ${totalMaxScore} </b> Silahkan submit`;
        }
        else if(totalMaxScore > nilai){
            document.getElementById('btnAssign').setAttribute('disabled', true);
            document.getElementById("konfirmasiScore").innerHTML = `Total Score Max <b class="text-red" id="totalScore"> ${totalMaxScore} </b> Kelebihan <b class="text-red" id="totalScore"> ${Math.abs(sisaMaxScore)} </b>`;
        }
        else{
            document.getElementById('btnAssign').setAttribute('disabled', true);
            document.getElementById("konfirmasiScore").innerHTML = `Total Score Max <b class="text-red" id="totalScore"> ${totalMaxScore} </b> Kurang <b class="text-red" id="totalScore"> ${sisaMaxScore} </b>`;
        }
    }

    function hitungTotalMaxScore() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"][name="assessment_poin_id[]"]');
        var totalMaxScore = 0;

        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                // // Gantilah ini dengan cara Anda mengambil max_score dari masing-masing checkbox yang dicentang
                // var maxScore = parseFloat(checkbox.getAttribute('data-score-max')); // Misalnya, menggunakan atribut data-score-max
                // totalMaxScore += maxScore;
                var row = dataTable.row($(checkbox).closest('tr')).data();
                var maxScore = parseFloat(row.score_max); // Asumsi nilai max_score ada di kolom 'score_max'
                totalMaxScore += maxScore;
            }
        });
        // console.log(totalMaxScore);
        return totalMaxScore;

    }

    function get_data_min_score(event_id, category){
        let result_data

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            async: false,
            type: 'GET',
            url: '{{ route('query.customassesment') }}',
            dataType: 'json',
            data: {
                table: `minimumscore_events`,
                where: {
                    'event_id': event_id,
                    'category': category
                },
                limit: 1
            },
            success: function(response) {
                // console.log(response[0]);
                result_data = response[0]
            },
            error: function(xhr, status, error) {
                // console.error(xhr.responseText);
                result_data = []
            }
        })
        // console.log(result_data);
        $(`#minimumscore_oda`).val(result_data.score_minimum_oda)
        $(`#minimumscore_pa`).val(result_data.score_minimum_pa)
    }

    count_validasi = new Set()
    function validasi_minimum_score(elemen, score){
        if(elemen.value > score){
            $(`#${elemen.id}`).addClass('is-invalid')
            // $(`#br-${id_split[1]}-${id_split[2]}`).hide()
            count_validasi.add(`${elemen.id}`)
        }else{
            $(`#${elemen.id}`).removeClass('is-invalid')
            // $(`#br-${id_split[1]}-${id_split[2]}`).show()
            count_validasi.delete(`${elemen.id}`)
        }

        if(count_validasi.size){
            $('#btnAssign').prop('disabled', true)
        }else{
            $('#btnAssign').prop('disabled', false)
        }
    }

    // Memeriksa apakah pesan sukses ditampilkan setelah tombol "Kirim" diklik sebelumnya
    var successMsgDisplayed = "{{ session('success') ? 'true' : 'false' }}";
    if (successMsgDisplayed === 'true') {
        $('#btnAssign').prop('disabled', true);
    }

    // Event listener saat tombol "Kirim" diklik
    $('#btnAssign').on('click', function() {
        // Menyimpan status tombol "Kirim" ke localStorage
        localStorage.setItem('btnAssignClicked', 'true');
    });

    // Memeriksa status tombol "Kirim" saat halaman dimuat ulang
    if (localStorage.getItem('btnAssignClicked') === 'true') {
        $('#btnAssign').prop('disabled', true);
    }

    function get_data_event(event_id){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            async: false,
            type: 'GET',
            url: '{{ route('query.customassesment') }}',
            dataType: 'json',
            data: {
                table: `events`,
                where: {
                    'events.id': event_id
                },
                join: {
                    'companies':{
                        'companies.company_code': 'events.company_code'
                    },
                },
                select:[
                    'event_name',
                    'year',
                    'company_name'
                ],
                limit: 5
            },
            success: function(response) {
                $('#event_name').text(`${response[0].event_name}`)
                $('#event_year').text(`${response[0].year}`)
                if(response.length > 1){
                    $('#event_company').text(`${response[0].company_name} ${response[1].company_name}`)
                }else{
                    $('#event_company').text(`${response[0].company_name}`)
                }

            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        })
    }
</script>
@endpush
