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
        .ai-analyze-bar{
            background: linear-gradient(90deg,#ef4444,#b91c1c);
            border-radius: 999px;
            padding: 10px 14px;
            display:flex; align-items:center; justify-content:space-between;
            box-shadow:0 6px 18px rgba(0,0,0,.07);
        }

        .ai-analyze-left{display:flex; align-items:center; gap:10px; color:#fff; font-weight:600; }
        .ai-dot{width:6px; height:6px; background:#fff; border-radius:50%; display:inline-block}
        .ai-title{opacity:.95}
        .ai-actions{display:flex; align-items:center; gap:8px}
        .ai-btn{
            background:#fff; color:#111; border:none; border-radius:14px; padding:8px 14px;
            font-weight:700; box-shadow:0 3px 10px rgba(0,0,0,.08);
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
                            Input Penilaian Presentasi
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('assessment.presentation') }}">
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
                    <label class="small mb-1 fw-600" for="inputFirstName">Kategori Inovasi</label>
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
                <div class="row mb-3">
                    <div class="d-flex flex-column align-items-start">
                        @if ($datas->full_paper || $datas->file_review)
                            <a href="{{ route('paper.watermarks', ['paper_id' => $datas->paper_id]) }}?rand={{ uniqid() }}" class="btn btn-sm text-white" style="background-color: #e84637" target="_blank">
                                Lihat Makalah
                            </a>
                            <a href="{{ route('assessment.benefitView', ['paperId' => $datas->paper_id]) }}" class="btn btn-sm text-white mt-2" style="background-color: #e84637" target="_blank">
                                Lihat Berita Acara Benefit
                            </a>
                            <button class="btn btn-sm text-white mt-2"
                                style="background-color: #e84637"
                                data-bs-toggle="modal"
                                data-bs-target="#showDocument"
                                onclick="show_document_modal({{ $datas->event_team_id }})">
                                Lihat Dokumen Pendukung
                            </button>

                            @if ($datas->full_paper_updated_at)
                                <small class="text-muted mt-2">
                                    <small class="text-muted mt-2">
                                        Makalah Terakhir diubah pada:
                                        {{ \Carbon\Carbon::parse($datas->full_paper_updated_at)->translatedFormat('d F Y H:i') }}
                                    </small>

                                </small>
                            @else
                                <small class="text-muted mt-2">
                                    Terakhir diubah pada: Tidak tersedia
                                </small>
                            @endif
                        @else
                            <p class="text-muted">File paper belum tersedia.</p>
                        @endif
                    </div>
                </div>
                <div class="row col-md-6 col-sm-6 col-xs-6">
                    <x-assessment-matrix.show-image-button />
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">Form Penilaian Presentasi</div>
            {{-- Analisis Makalah (AI) --}}
            <!--<div class="ai-analyze-bar mb-3">-->
            <!--<div class="ai-analyze-left">-->
            <!--    <span class="ai-title">Analisis Makalah dengan AI</span>-->
            <!--</div>-->

            <!--<div class="ai-actions">-->
            <!--    <a class="ai-btn" target="_blank"-->
            <!--    href="{{ route('ai.analyze.paper.view', ['paperId' => $datas->paper_id]) }}">-->
            <!--    Analisis Makalah-->
            <!--    </a>-->

            <!--    <form id="aiAnalyzeForm" class="d-none" target="_blank"-->
            <!--        action="{{ route('ai.analyze.paper', ['paperId' => $datas->paper_id]) }}"-->
            <!--        method="POST">-->
            <!--    @csrf-->
            <!--    </form>-->
            <!--</div>-->
            <!--</div>-->
            
            {{-- Analisis Makalah (AI) --}}
            <div class="ai-analyze-bar mb-3">
                <div class="ai-analyze-left">
                    <span class="ai-title">Analisis Makalah dengan AI</span>
                </div>

                <div class="ai-actions">
                    <a class="ai-btn" target="_blank"
                    href="{{ route('ai.analyze.paper.view', ['paperId' => $datas->paper_id, 'stage' => 'presentation']) }}">
                    Analisis Makalah
                    </a>

                    <form id="aiAnalyzeForm" class="d-none" target="_blank"
                        action="{{ route('ai.analyze.paper', ['paperId' => $datas->paper_id]) }}"
                        method="POST">
                    @csrf
                    <input type="hidden" name="stage" value="presentation">
                    </form>
                </div>
            </div>

             {{-- Form Penilaian Juri --}}
            <form action="{{ route('assessment.submitJuri', ['id' => Request::segments()[2]]) }}" method="post">
                @csrf
                @method('put')
                <div class="card-body">
                    <div class="w-100 mx-auto text-center">
                        <p class="mb-0" style="font-weight: bold;">Total Skor On Desk:</p> 
                        <p class="mt-0 text-primary" style="font-size: 1.7rem; font-weight: 400">{{ $datas->score_ondesk }}</p>
                    </div>
                    <hr>
                    <table id="datatable-penilaian"></table>
                    <hr>
                    <div class="mb-3 mx-auto">
                        <x-assessment.deviation-information 
                            :event-team-id="Request::segments()[2]" 
                            :assessment-stage="'presentation'" />
                    </div>
                    <hr>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1 fw-600" for="inputRecomCategory">Rekomendasi Kategori</label>
                        <textarea name="recommendation" id="inputRecomCategory" cols="30" rows="3" class="form-control"
                        {{ auth()->user()->role === 'Admin' ? 'disabled' : '' }}>{{ $sofiData->recommend_category }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1 fw-600" for="inputStrength">Kekuatan Inovasi</label>
                        <textarea name="sofi_strength" id="inputStrength" cols="30" rows="3" class="form-control"
                        {{ auth()->user()->role === 'Admin' ? 'disabled' : '' }}>{{ $sofiData->strength }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1 fw-600" for="inputOpportunity">Peluang inovasi</label>
                        <textarea name="sofi_opportunity" id="inputOpportunity" class="form-control" cols="30" rows="3"
                        {{ auth()->user()->role === 'Admin' ? 'disabled' : '' }}>{{ $sofiData->opportunity_for_improvement }}</textarea>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1 fw-600" for="inputCommentBenefit">Komentar Benefit</label>
                        <textarea name="suggestion_for_benefit" id="inputCommentBenefit" class="form-control" cols="30" rows="3"
                        {{ auth()->user()->role === 'Admin' ? 'disabled' : '' }}>{{ $sofiData->suggestion_for_benefit }}</textarea>
                    </div>
                    <input type="hidden" name="updated_at" value="{{ $datas->updated_at->format('Y-m-d H:i:s') }}">
                    <input type="hidden" name="stage" value="assessment-presentation-value">
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1 fw-600" for="inputFinancialBenefit">Benefit Finansial <small>Inputkan Angka</small></label>
                        <input 
                            type="text"
                            name="financial_benefit" 
                            id="inputFinancialBenefit" 
                            class="form-control w-100" 
                            value="{{ number_format($datas->financial, 0, ',', '.') }}" 
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            {{ auth()->user()->role === 'Admin' ? 'disabled' : 'required' }}
                        >
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="small mb-1 fw-600" for="inputPotentialBenefit">Benefit Potensial <small>Inputkan Angka</small></label>
                        <input 
                            type="text"
                            name="potential_benefit" 
                            id="inputPotentialBenefit" 
                            class="form-control w-100" 
                            value="{{ number_format($datas->potential_benefit, 0, ',', '.') }}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            {{ auth()->user()->role === 'Admin' ? 'disabled' : 'required' }}
                        >
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
                    @if (Auth::user()->role == 'Juri' || $is_judge || Auth::user()->role == 'Superadmin')
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="btnsubmit">Submit Nilai</button>
                    </div>
                    @endif
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
                        <select class="js-example-basic-multiple" 
                                name="judge_id[]" 
                                id="select2-juri" 
                                multiple="multiple" 
                                style="width: 100%">
                            @foreach ($availableJudges as $judge)
                                <option value="{{ $judge->id }}">{{ $judge->name }}</option>
                            @endforeach
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

    <!--{{-- modal delete juri --}}-->
    <!--<div class="modal fade" id="deleteJuri" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"-->
    <!--    aria-hidden="true">-->
    <!--    <div class="modal-dialog modal-dialog-centered" role="document">-->
    <!--        <div class="modal-content">-->
    <!--            <div class="modal-header">-->
    <!--                <h5 class="modal-title" id="exampleModalCenterTitle">Hapus Juri</h5>-->
    <!--                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>-->
    <!--            </div>-->
    <!--            <form action="{{ route('assessment.deleteJuri') }}" method="post">-->
    <!--                @csrf-->
    <!--                <div class="modal-body">-->
    <!--                    <div class="col-md-12">-->
    <!--                        <input type="text" name="event_team_id" value="{{ $datas->event_team_id }}" hidden>-->
    <!--                        <input type="hidden" name="stage" value="presentation">-->
    <!--                        <label for="dataJudge">Pilih Juri</label>-->
    <!--                        <select name="judge_id" class="form-select" id="">-->
    <!--                            @foreach ($datas_juri as $data_juri)-->
    <!--                                <option value="{{ $data_juri->judge_id }}"> {{ $data_juri->employee_id }} --->
    <!--                                    {{ $data_juri->name }}</option>-->
    <!--                            @endforeach-->
    <!--                        </select>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--                <div class="modal-footer">-->
    <!--                    <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Tutup</button>-->
    <!--                    <button class="btn btn-danger" type="submit">Hapus</button>-->
    <!--                </div>-->
    <!--            </form>-->
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    
    {{-- modal delete juri --}}
    <div class="modal fade" id="deleteJuri" tabindex="-1" aria-labelledby="deleteJuriLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteJuriLabel">Form Hapus Juri</h5>
            <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
    
          <form action="{{ route('assessment.deleteJuri') }}" method="post" id="formDeleteJuri">
            @csrf
            <div class="modal-body">
              <input type="hidden" name="event_team_id" value="{{ $datas->event_team_id }}">
              <input type="hidden" name="stage" value="presentation">
    
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="deleteAll" name="delete_all" value="1">
                <label class="form-check-label" for="deleteAll">
                  Hapus semua juri pada stage ini
                </label>
              </div>
    
              <select name="judge_ids[]" id="judge_ids" class="form-select" multiple size="8">
                @foreach ($datas_juri as $data_juri)
                  <option value="{{ $data_juri->judge_id }}">
                    {{ $data_juri->employee_id }} - {{ $data_juri->name }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">Gunakan Ctrl/Cmd untuk memilih banyak item.</small>
            </div>
    
            <div class="modal-footer">
              <button class="btn btn-outline-primary" type="button" data-bs-dismiss="modal">Tutup</button>
              <button class="btn btn-danger" type="submit">Hapus</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    
    {{-- Modal show beberapa dokumen --}}
    <div class="modal fade" id="showDocument" tabindex="-1" role="dialog" aria-labelledby="showDocumentTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white border-bottom-0">
                    <h5 class="modal-title fw-bold text-white" id="showDocumentTitle">Dokumen Pendukung</h5>
                    <button class="btn-close btn-close-white" type="button" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div id="resultContainer"></div>
                    </div>
                </div>

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
                    "url": "{{ route('query.get_input_pa_assessment_team') }}",
                    "type": "GET",
                    "async": true,
                    "dataSrc": function (data) {
                        return data.data;
                    },
                    "data": function (d) {
                        d.filterEventTeamId = {{ Request::segments()[2] }};
                    }
                },
                "columns": columns,
                "scrollY": true,
                "stateSave": true,
                "destroy": true,
                "paging": false
            });
            return dataTable;
        }

        function updateColumnDataTable() {
            newColumn = []
            $.ajax({
                url: "{{ route('query.get_input_pa_assessment_team') }}",
                method: 'GET',
                data: {
                    filterEventTeamId: {{ Request::segments()[2] }}
                },
                async: false,
                success: function (data) {
                    if (data.data.length) {
                        let row_column = {
                            'data': "DT_RowIndex",
                            'title': "No",
                            'mData': "DT_RowIndex",
                            'sTitle': "No"
                        };
                        newColumn.push(row_column);
                        for (var key in data.data[0]) {
                            if (key !== "DT_RowIndex") {
                                let row_column = {
                                    'data': key,
                                    'title': key === "point" ? "Poin" : key,
                                    'mData': key,
                                    'sTitle': key === "point" ? "Poin" : key
                                };
                                newColumn.push(row_column);
                            }
                        }
                    } else {
                        let row_column = {
                            'data': '',
                            'title': '',
                            'mData': '',
                            'sTitle': ''
                        };
                        newColumn.push(row_column);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Gagal mengambil kolom: ' + error);
                }
            });
            return newColumn;
        }

        $(document).ready(function () {
            let column = updateColumnDataTable();
            let dataTable = initializeDataTable(column);
            
            function show_document_modal(eventTeamId){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'GET',
                    url: '/assessment/view-supporting-document/' + eventTeamId, // Route langsung
                    dataType: 'json',
                    success: function(response) {
                        $('#resultContainer').empty();
                        var container = $('#resultContainer');
            
                        response.forEach(function(item) {
                            var fileUrl = '{{ route('query.getFile') }}' + '?directory=' + item.path;
            
                            // Menampilkan gambar
                            if (item.file_name.toLowerCase().endsWith('.jpg') || item.file_name.toLowerCase().endsWith('.jpeg') || item.file_name.toLowerCase().endsWith('.png')) {
                                var img = $('<img>', {
                                    src: fileUrl,
                                    class: 'w-100 my-2',
                                    alt: item.file_name
                                });
                                container.append(img);
                            }
            
                            // Menampilkan PDF
                            else if (item.file_name.toLowerCase().endsWith('.pdf')) {
                                var iframe = $('<iframe>', {
                                    src: fileUrl,
                                    width: '100%',
                                    height: '720px',
                                    class: 'my-2'
                                });
                                container.append(iframe);
                            }
            
                            // Menampilkan video mp4
                            else if (item.file_name.toLowerCase().endsWith('.mp4')) {
                                var video = $('<video>', {
                                    src: fileUrl,
                                    class: 'w-100 my-2',
                                    controls: true
                                });
                                container.append(video);
                            }
            
                            // Format tidak didukung (mkv, avi, dll)
                            else {
                                container.append('<p>Format tidak didukung untuk preview: ' + item.file_name + '</p>');
                                var downloadLink = $('<a>', {
                                    href: fileUrl,
                                    class: 'btn btn-primary mb-2',
                                    download: item.file_name,
                                    text: 'Download ' + item.file_name
                                });
                                container.append(downloadLink);
                            }
            
                            // Form delete
                            var form = $('<form>', {
                                method: 'POST',
                                action: '{{ route('paper.deleteDocument') }}'
                            });
            
                            form.append($('<input>', {
                                type: 'hidden',
                                name: '_method',
                                value: 'DELETE'
                            }));
            
                            form.append($('<input>', {
                                type: 'hidden',
                                name: 'id',
                                value: item.id
                            }));
            
                            form.append($('<input>', {
                                type: 'hidden',
                                name: '_token',
                                value: '{{ csrf_token() }}'
                            }));
            
                            var deleteBtn = $('<button>', {
                                type: 'submit',
                                class: 'btn btn-danger my-3',
                                text: 'Delete'
                            });
            
                            form.append(deleteBtn);
                            container.append(form);
                            container.append('<hr>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error response:", xhr.responseText);
                    }
                });
            }
            
            // Hapus isi modal saat ditutup
            function remove_document_modal() {
                $('#resultContainer').empty();
            }
            
            $('#showDocument').on('hidden.bs.modal', function () {
                remove_document_modal();
            });
            window.show_document_modal = show_document_modal;
        });
        
        // In your Javascript (external .js resource or <script> tag)
         $('#select2-juri').select2({
            dropdownParent: $("#addJuri"),
            allowClear: true,
            width: "100%",
            placeholder: "Pilih Employee untuk dijadikan juri",
            ajax: {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/approveadminuery/get-judge',
                type: 'GET',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        query: params.term,
                        event_id: {{ $datas->event_id }} // Pastikan ini di blade file
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                id: item.judges_id,
                                text: item.employee_id + ' - ' + item.name
                            };
                        })
                    };
                },
                cache: true
            }
        });

        count_exceed_max_score = new Set()
        function validate_score(elemen){
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
    
    //AI
        // document.getElementById('runAnalyzeBtn')?.addEventListener('click', function(){
        // document.getElementById('aiAnalyzeForm')?.submit();
        // });    

    </script>
@endpush
