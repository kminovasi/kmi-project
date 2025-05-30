@extends('layouts.app')
@section('title', 'Data Assessment Template')
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        #textarea {
            width: 100%;
            height: auto;
        }

        .active-link {
            color: #ffc004;
            background-color: #e81500;
        }

        input[type="number"] {
            width: 150px;
            /* Atur lebar sesuai kebutuhan */
            height: 20px;
            /* Atur tinggi sesuai kebutuhan */
            font-size: 12px;
        }

        .btn-right {
            margin-left: auto;
            width: 10rem;
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
                            Input Penilaian Caucus
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('assessment.caucus.data') }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
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

        <!-- Account details card-->
        <div class="card mb-4">
            <div class="card-header">Detail Tim</div>
            <div class="card-body">
                <!-- Form Group (first name)-->
                <div class="col-md-12 mb-3">
                    <label class="small mb-1 fw-600" for="inputFirstName">Nama Tim</label>
                    <input class="form-control" id="inputFirstName" type="text" value="{{ $datas->team_name }}" readonly>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="small mb-1 fw-600" for="inputFirstName">Judul Inovasi</label>
                    <input class="form-control" id="inputFirstName" type="text" placeholder="Enter your first name"
                        value="{{ $datas->innovation_title }}" readonly>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="small mb-1 fw-600" for="inputFirstName">Kategori</label>
                    <input class="form-control" id="inputFirstName" type="text" placeholder="Enter your first name"
                        value="{{ $datas->category_name }}" readonly>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="small mb-1 fw-600" for="inputFirstName">Juri</label>
                    <div class="table-responsive table-billing-history">
                        <table class="table mb-0">
                            <tbody>
                                @foreach ($datas_juri as $data_juri)
                                    <tr>
                                        <td>{{ $data_juri->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <x-assessment-matrix.show-image-button />
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Form Penilaian Caucus</div>
            <form action="{{ route('assessment.submitJuri', ['id' => Request::segments()[2]]) }}" method="post">
                @csrf
                @method('put')
                <div class="card-body">
                    <table id="datatable-penilaian"></table>
                    <hr>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1" for="inputRecomCategory">Rekomendasi Kategori</label>
                        <textarea name="recommendation" id="inputRecomCategory" cols="30" rows="3" class="form-control">{{ $sofiData->recommend_category }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1" for="inputStrength">Kekuatan Inovasi</label>
                        <textarea name="sofi_strength" id="inputStrength" cols="30" rows="3" class="form-control">{{ $sofiData->strength }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1" for="inputOpportunity">Peluang Inovasi</label>
                        <textarea name="sofi_opportunity" id="inputOpportunity" class="form-control" cols="30" rows="3">{{ $sofiData->opportunity_for_improvement }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1" for="inputCommentBenefit">Komentar Benefit</label>
                        <textarea name="suggestion_for_benefit" id="inputCommentBenefit" class="form-control" cols="30" rows="3">{{ $sofiData->suggestion_for_benefit }}</textarea>
                    </div>
                </div>
                <div class="card-footer">
                    @if (Auth::user()->role == 'Admin' || Auth::user()->role == 'Superadmin')
                        <div class="row">
                            <div class="col-md-6 mb-2 d-grid">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#addJuri">Tambah Juri</button>
                            </div>
                            <div class="col-md-6 mb-2 d-grid">
                                <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal"
                                    data-bs-target="#deleteJuri">Hapus Juri</button>
                            </div>
                        </div>
                    @endif
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="btnsubmit">Submit Nilai</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- modal add juri --}}
    <div class="modal fade" id="addJuri" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Form Tambah Juri</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('assessment.addJuri') }}" method="post">
                    @csrf
                    <input type="text" name="event_team_id" value="{{ $datas->event_team_id }}" hidden>
                    <input type="text" name="stage" value="presentation" hidden>
                    <div class="modal-body">
                        <div class="col-md-12">
                            <label for="dataJudge">Pilih Juri</label>
                            <select class="js-example-basic-single" name="judge_id" z-index="10" id="select2-juri">
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-danger" type="button" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-primary" type="submit">Pilih</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal dekete juri --}}
    <div class="modal fade" id="deleteJuri" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Hapus Juri</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('assessment.deleteJuri') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="col-md-12">
                            <input type="text" name="event_team_id" value="{{ $datas->event_team_id }}" hidden>
                            <input type="hidden" name="stage" value="caucus">
                            <label for="dataJudge">Pilih Juri</label>
                            <select name="judge_id" class="form-select" id="">
                                @foreach ($datas_juri as $data_juri)
                                    <option value="{{ $data_juri->judge_id }}"> {{ $data_juri->employee_id }} -
                                        {{ $data_juri->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Tutup</button>
                        <button class="btn btn-danger" type="submit">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="">
    function initializeDataTable(columns) {
        var dataTable = $('#datatable-penilaian').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('query.get_input_caucus_assessment_team') }}",
                "type": "GET",
                "async": false,
                "dataSrc": function (data) {
                    // console.log(columns);
                    // console.log(data.data);
                    return data.data;
                },
                "data": function (d) {
                    d.filterEventTeamId = {{ Request::segments()[2] }};
                    // d.filterYear = $('#filter-year').val();
                    // d.filterCategory = $('#filter-category').val();
                }
            },
            "columns": columns,
            "scrollY": true,
            // "scrollX": true,
            "stateSave": true,
            "destroy": true,
            "paging": false

        });
        return dataTable;
    }

    function updateColumnDataTable() {
        newColumn = []
        $.ajax({
            url: "{{ route('query.get_input_caucus_assessment_team') }}", // Misalnya, URL untuk mengambil kolom yang dinamis
            method: 'GET',
            // dataType: 'json',
            data:{
                filterEventTeamId: {{ Request::segments()[2] }}
            },
            async: false,
            success: function (data) {
                // newColumn = []
                console.log(data.data)
                if(data.data.length){
                    let row_column = {};
                    row_column['data'] = "DT_RowIndex"
                    row_column['title'] = "No"
                    row_column['mData'] = "DT_RowIndex"
                    row_column['sTitle'] = "No"
                    newColumn.push(row_column)
                    for( var key in data.data[0]){
                        if(key != "DT_RowIndex"){
                            let row_column = {};
                            row_column['data'] = key
                            row_column['title'] = key
                            row_column['mData'] = key
                            row_column['sTitle'] = key
                            newColumn.push(row_column)
                        }
                    }
                }else{
                    let row_column = {};
                    row_column['data'] = ''
                    row_column['title'] = ''
                    row_column['mData'] = ''
                    row_column['sTitle'] = ''
                    newColumn.push(row_column)
                }
            },
            error: function (xhr, status, error) {
                console.error('Gagal mengambil kolom: ' + error);
            }
        });
        console.log("ini yang di fungsi update");
        console.log(newColumn);
        return newColumn
    }

    $(document).ready(function() {

        let column = updateColumnDataTable();
        // column = []
        let dataTable = initializeDataTable(column);
    });

    // In your Javascript (external .js resource or <script> tag)
    $(document).ready(function() {
        $('#select2-juri').select2({
            // allowClear: true,
            // theme: "classic",
            dropdownParent: $("#addJuri"),
            allowClear: true,
            width: "100%",
            placeholder: "Pilih Employee untuk dijadikan juri",
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET', // Metode HTTP POST
                url: '{{ route('query.custom') }}',
                dataType: 'json',
                delay: 250, // Penundaan dalam milidetik sebelum permintaan AJAX dikirim
                data: {
                    table: "judges",
                    where: {
                        'event_id': {{ $datas->event_id }},
                        'status': 'active'
                    },
                    limit: 100,
                    join: {
                        'users':{
                            'users.employee_id': 'judges.employee_id'
                        }
                    },
                    select:[
                        'judges.id as judges_id',
                        'users.employee_id as employee_id',
                        'users.name as name'
                    ]
                },
                processResults: function(data) {
                    // Memformat data yang diterima untuk format yang sesuai dengan Select2
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.employee_id + ' - ' + item
                                    .name, // Nama yang akan ditampilkan di kotak seleksi
                                id: item.judges_id // Nilai yang akan dikirimkan saat opsi dipilih
                            };
                        })
                    };
                },
                cache: true,
            }
        });
    });

    count_exceed_max_score = new Set()
    function validate_score(elemen){
        // console.log(+$(`#${elemen.id.split('-')[2]}`).text() < elemen.value);
        id_split = elemen.id.split('-')
        if(+$(`#${id_split[2]}`).text() < elemen.value){
            $(`#input-${id_split[1]}-${id_split[2]}`).addClass('is-invalid')
            $(`#br-${id_split[1]}-${id_split[2]}`).hide()
            count_exceed_max_score.add(`${id_split[1]}-${id_split[2]}`)
        }else{
            $(`#input-${id_split[1]}-${id_split[2]}`).removeClass('is-invalid')
            $(`#br-${id_split[1]}-${id_split[2]}`).show()
            count_exceed_max_score.delete(`${id_split[1]}-${id_split[2]}`)
        }
        // console.log(count_exceed_max_score.size);
        if(count_exceed_max_score.size){
            $('#btnsubmit').prop('disabled', true)
        }else{
            $('#btnsubmit').prop('disabled', false)
        }
    }
</script>
@endpush
