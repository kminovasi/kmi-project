@extends('layouts.app')
@section('title', 'Data Assessment Template')
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
                        Show Template Assessment
                    </h1>

                </div>
                <div class="col-12 col-xl-auto mb-3">
                    <a class="btn btn-sm btn-light text-primary" href="{{ route('assessment.create.template') }}">
                        <i class="me-1" data-feather="plus"></i>
                        Create Point
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Main page content-->
<div class="container-xl px-4 mt-4">
    <div class="p-2 border-bottom">
        @if (Auth::user()->role == 'Admin')
        <a href="{{ route('assessment.show.point') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.show.point') ? 'active-link' : '' }}">Assessment
            Point</a>
        @elseif (auth()->check() && auth()->user()->role == 'Superadmin')
        <a href="{{ route('assessment.show.template') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.show.template') ? 'active-link' : '' }}">Template
            Assessment</a>
        <a href="{{ route('assessment.show.point') }}" class="btn btn-outline-danger btn-sm rounded shadow-sm px-4 py-3 text-uppercase fw-800 me-2 my-1 {{ Route::is('assessment.show.point') ? 'active-link' : '' }}">Assessment
            Point Setting</a>
        @endif

    </div>
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

    <!--Modal Ceklist Event Template Assessment-->
    <div class="card mb-4 col-12">
        <div class="card-body">
            <div class="mb-3">
                <button class="btn btn-outline-primary btn-sm" style="margin-right: 10px;" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
                <button id="select-all-button" class="btn btn-outline-primary btn-sm">Select All</button>
            </div>
            <form action="{{ route('assessment.store.assign.point') }}" method="POST">
                @csrf
                @method('POST')
                <table id="datatable-assessment" class="display">
                </table>
                <hr>
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <h6 class="card-title text-primary mb-3 d-flex align-items-center">
                            <i class="me-2" data-feather="info"></i>
                            <span>Information</span>
                        </h6>
                        <div id="konfirmasiScore" class="bg-light p-3 rounded border">
                            <!-- Content for the information will go here -->
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <input type="text" name="category" id="inputCategory" hidden>
                            <h6 for="">Pilih Event</h6>
                            @foreach ($events as $event)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="{{ $event->id }}" name="events[]" id="event{{ $event->id }}" onchange="cek()">
                                <label class="form-check-label" for="event{{ $event->id }}">
                                    {{ $event->event_name }} - {{ $event->year }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="btnAssign" onclick="lakukanValidasi()" disabled>Kirim</button>
                        </div>
                    </div>
                </div>

            </form>
            {{-- {{ dd(session()->all()) }} --}}
        </div>
    </div>
</div>

{{-- modal untuk update template --}}
<div class="modal fade" id="updateTemplate" tabindex="-1" aria-labelledby="updateTemplateTitle" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateTemplateTitle">Update Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateTemplateAssessment" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="inputPoint">Point Assessment</label>
                        <input type="text" class="form-control" name="point" id="inputPoint" value="" placeholder="Enter the point value">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="inputDetailPoint">Detail</label>
                        <textarea name="detail_point" id="inputDetailPoint" rows="5" class="form-control" placeholder="Enter the details here..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="dataStage">Stage</label>
                        <select name="stage" id="dataStage" class="form-select">
                            <option value="on desk">On Desk</option>
                            <option value="presentation">Presentation</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="inputPDCA">Comment</label>
                        <select name="pdca" id="inputPDCA" class="form-select">
                            <option value="Plan">PLAN</option>
                            <option value="Do">DO</option>
                            <option value="Check">CHECK</option>
                            <option value="Action">ACTION</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="inputScoreMax">Max Score</label>
                        <input type="number" class="form-control" name="score_max" id="inputScoreMax" value="" placeholder="Enter max score">
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


{{-- modal untuk delete template assessment --}}
<div class="modal fade" id="deleteTemplate" tabindex="-1" role="dialog" aria-labelledby="deleteTemplateTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="deleteTemplateAssessment" method="POST">
            @csrf
            @method ('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTemplateTitle">Konfirmasi Hapus Data</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah yakin data ini akan dihapus ?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-danger" type="submit">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- modal untuk filter Template Assessment--}}
<div class="modal fade" id="filterModal" role="dialog" aria-labelledby="filterModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title d-flex align-items-center" id="filterModalTitle">
                    <i class="me-2" data-feather="filter"></i> Filter Options
                </h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-3">
                <!-- Category Filter -->
                <div class="mb-4">
                    <label class="mb-1 fw-bold text-muted" for="filter-category">Category</label>
                    <select id="filter-category" class="form-select shadow-sm" name="filter-category">
                        <option value="BI/II">Implemented</option>
                        <option value="IDEA">IDEA Box</option>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script type="">
    $(document).ready(function() {

        document.getElementById('inputCategory').value = $('#filter-category').val()
        dataTable = $('#datatable-assessment').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('query.get.template.assessment') }}",
                "type": "GET",
                "dataSrc": function (data) {
                    console.log('Jumlah data total: ' + data.recordsTotal);
                    console.log('Jumlah data setelah filter: ' + data.recordsFiltered);
                    console.log('Jumlah data setelah filter: ' + data.data);
                    return data.data;
                },
                data: function (d) {
                    d.filterCategory = $('#filter-category').val();
                }
            },
            "columns": [
                {"data": "DT_RowIndex", "title": "No"},
                {"data": "point", "title": "Point Assessment"},
                {"data": "detail_point", "title": "Detail Assessment"},
                {"data": "pdca", "title": "Category"},
                {"data": "score_max", "title": "Max Score"},
                {"data": "assign","title": "Assign"
                //     "render": function (data, type, row) {
                //     // Check if checkbox is already checked on initial load
                //     let isChecked = data ? 'checked' : '';
                //     return `<input type="checkbox" name="assessment_poin_id[]" value="${row.id}" ${isChecked}>`;
                // }
                },
                {"data": "action","title": "Action"},
                {"data": "stage", "title": "Stage"}
            ],

            "scrollY": true,
            "paging": false,
            "searching" : false,
            "ordering" : false,
        });

        var totalMaxScore = 0;
        cek(); // Call cek() to calculate initial score

        $('#filter-category').on('change', function () {
            dataTable.ajax.reload();
            cek();
            document.getElementById('inputCategory').value = $('#filter-category').val()
        });

        $('#datatable-assessment tbody input[type="checkbox"]').on('change', function() {
            cek();
        });

        // Inisialisasi 'selectAllButton' dan set 'active' menjadi false
        const selectAllButton = $('#select-all-button');
        selectAllButton.removeClass('active');

         // Saat table selesai dirender, hilangkan centang pada semua checkbox
        // dataTable.on('draw.dt', function () {
        //     $('#datatable-assessment tbody input[type="checkbox"]').prop('checked', false);
        //     selectAllButton.removeClass('active');
        //     cek(); // Recalculate score after unchecking
        // });

        $('#select-all-button').on('click', function () {
        // Get all checkboxes in the 'assign' column
        const checkboxes_assign = $('#datatable-assessment tbody input[type="checkbox"]');

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

    function get_data_template(template_id) {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET', // Metode HTTP POST
            url: '{{ route('query.custom') }}',
            dataType: 'json',
            data: {
                table: "template_assessment_points",
                where: {
                    "id": template_id
                },
                limit: 1
            },
            success: function(response) {
                // companyField.value = response[0].company_name
                document.getElementById("inputPoint").value = response[0].point
                document.getElementById("inputScoreMax").value = response[0].score_max
                //document.getElementById("inputDetailPoint").value = response[0].detail_point;
                var inputDetailPoint = document.getElementById("inputDetailPoint"); //agar detail bisa ditambah tapi tdk dapat dihapus
                inputDetailPoint.defaultValue = response[0].detail_point;
                inputDetailPoint.value = inputDetailPoint.defaultValue;

                var isDeleting = false;
                // inputDetailPoint.addEventListener("keydown", function(event) {
                //     if (event.key === "Backspace" || event.key === "Delete") {
                //         isDeleting = true;
                //     }
                // });

                inputDetailPoint.addEventListener("input", function(event) {
                    if (isDeleting && event.target.value.length < inputDetailPoint.defaultValue.length) {
                        event.target.value = inputDetailPoint.defaultValue;
                        isDeleting = false;
                    }
                });

                //select category
                var selectElement = document.getElementById("inputPDCA").value = response[0].pdca;
                for (var i = 0; i < selectElement.options.length; i++) {
                var option = selectElement.options[i];
                    if (option.value === selectedValue) {
                        option.selected = true;
                    } else {
                        option.selected = false;
                    }
                }
            },
            error: function(xhr, status, error) {
                // Tangani kesalahan jika ada
                console.error(xhr.responseText);
            }
        });
        //link untuk update
        var form = document.getElementById('updateTemplateAssessment');
        var url = `{{ route('assessment.update.template', ['id' => ':template_id']) }}`;
        url = url.replace(':template_id', template_id);
        form.action = url;

    }

    function delete_template(template_id) {
    // Mengatur ID data yang akan dihapus dalam variabel JavaScript
        var form = document.getElementById('deleteTemplateAssessment');
        var url = `{{ route('assessment.delete.template', ['id' => ':template_id']) }}`;
        url = url.replace(':template_id', template_id);
        form.action = url;
    }

    function cek(){
        // year = document.getElementById('year').value
        var checkboxes = document.querySelectorAll('input[name="events[]"]');
        totalMaxScore = hitungTotalMaxScore();

        var atLeastOneChecked = Array.from(checkboxes).some(function(checkbox) {
            return checkbox.checked;
        });

        var selectElement = document.getElementById("filter-category");
        var selectedValue = selectElement.value;

        if (selectedValue == "BI/II") {
    // Set nilai antara 920 hingga 1000
    nilai = Math.min(1000, Math.max(920, totalMaxScore));
} else {
    nilai = 100; // Untuk kasus selain "BI/II"
}

        sisaMaxScore = nilai - totalMaxScore;

        if(totalMaxScore == nilai){
            if(atLeastOneChecked && totalMaxScore == nilai){
                document.getElementById("konfirmasiScore").innerHTML = `Total Score Max <b class="text-green" id="totalScore"> ${totalMaxScore} </b> Silahkan submit`;
                document.getElementById('btnAssign').removeAttribute('disabled');
            }else{
                document.getElementById("konfirmasiScore").innerHTML = `Total Score Max <b class="text-green" id="totalScore"> ${totalMaxScore} </b> Silahkan pilih <b>Event</b> terlebih dahulu`;
                document.getElementById('btnAssign').setAttribute('disabled', true);
            }
        }else if(totalMaxScore > nilai){
            document.getElementById('btnAssign').setAttribute('disabled', true);
            document.getElementById("konfirmasiScore").innerHTML = `Total Score Max <b class="text-red" id="totalScore"> ${totalMaxScore} </b> Kelebihan <b class="text-red" id="totalScore"> ${Math.abs(sisaMaxScore)} </b>`;
        }else{
            document.getElementById('btnAssign').setAttribute('disabled', true);
            document.getElementById("konfirmasiScore").innerHTML = `Total Score Max <b class="text-red" id="totalScore"> ${totalMaxScore} </b> Kurang <b class="text-red" id="totalScore"> ${sisaMaxScore} </b>`;
        }


    }

  // Fungsi untuk menghitung total max_score dari checkbox yang dicentang
  function hitungTotalMaxScore() {
    var checkboxes = document.querySelectorAll('input[type="checkbox"][name="assessment_poin_id[]"]');
    var totalMaxScore = 0;

    checkboxes.forEach(function(checkbox) {
      if (checkbox.checked) {
        // Gantilah ini dengan cara Anda mengambil max_score dari masing-masing checkbox yang dicentang
       // var maxScore = parseFloat(checkbox.getAttribute('data-score-max')); // Misalnya, menggunakan atribut data-score-max
        var row = dataTable.row($(checkbox).closest('tr')).data();
        var maxScore = parseFloat(row.score_max); // Asumsi nilai max_score ada di kolom 'score_max'
        totalMaxScore += maxScore;
      }
    });
    return totalMaxScore;

  }



</script>
@endpush
