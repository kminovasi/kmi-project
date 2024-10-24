@extends('layouts.app')
@section('title', 'Data Paper')
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<style type="text/css">
    .step-one h1 {
        text-align: center;
    }
    .step-one img{
        width: 75%;
        height: 75%;
    }
    .step-one p{
        text-align: justify;
    }
    .file-review{
        margin:20px 10px;
    }
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
                            Data Judges
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('management-system.role.index') }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                        <a class="btn btn-sm btn-primary" href="{{ route('management-system.assign.juri.create') }}">
                            <i class="me-1" data-feather="plus"></i>
                            Assign Juri Event
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="mb-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                {{ session('success') }}

                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
        <div class="card mb-4">
            <div class="card-body">
                {{-- <div class="mb-3">
                    @if (Auth::user()->role == 'Admin')
                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
                    @endif
                </div> --}}
                <table id="datatable-judges">

                </table>
            </div>

        </div>
    </div>

{{-- modal untuk update judge --}}
<div class="modal fade" id="updateJudge" tabindex="-1" role="dialog" aria-labelledby="updateJudgeTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateJudgeTitle">Update Judge</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateJuri" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- <h6 class="small mb-1">Nama Juri</h6>
                    <input class="form-control" id="inputEmployeId" name="employee_id" value="" readonly/> --}}
                    <div class="mb-3">
                        <h6 class="small mb-1">Nama Juri</h6>
                        <input type="text" class="form-control" id="inputEmployeId" name="employee_name" value="" readonly>
                    </div>
                    <div class="mb-4">
                        <h6 class="small mb-1">Event</h6>
                        <select class="form-select" name="event_id" required>
                            @foreach ($datas_event as $ev)
                                <option value="{{ $ev->id }}">{{ $ev->event_name }} - {{ $ev->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <h6 class="small mb-1">Deskripsi</h6>
                        <textarea name="description" id="inDeskripsi" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    {{-- <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Revoke</button> --}}
                    <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" type="submit">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

        </div>
    </div>
    {{-- modal untuk revoke judge --}}
    <div class="modal fade" id="revokeJudge" tabindex="-1" role="dialog" aria-labelledby="revokeJudgeTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="revokeJudgeTitlw">Revoke Judge</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateStatusJuri" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input class="form-control" id="inputEmployeId" name="employee_id" type="hidden" value="" readonly/>

                        <p>Apakah yakin akan mencabut role juri ini ?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-danger" type="button" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary" type="submit" data-bs-dismiss="modal">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script type="">
    $(document).ready(function() {
        var dataTable = $('#datatable-judges').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('query.get_judge') }}",
                "type": "GET",
                "dataSrc": function (data) {
                    // console.log('Jumlah data total: ' + data.recordsTotal);
                    // console.log('Jumlah data setelah filter: ' + data.recordsFiltered);
                    // console.log('Jumlah data setelah filter: ' + data.data);
                    return data.data;
                }
            },
            "columns": [
                {
                    "data": null,
                    "title": "No",
                    "render": function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {"data": "name", "title": "Judge Name"},
                {"data": "description", "title": "Description"},
                {"data": "event_name", "title": "Event"},
                {"data": "status_juri", "title": "Status"},
                {"data": "action", "title": "Opsi"}
            ],

            "scrollY": true,
            "stateSave": true,
        });

    });

    function get_data_judge(judge_id){
         $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST', // Metode HTTP POST
            url: '{{ route('query.custom') }}',
            dataType: 'json',
            data: {
                table: "judges",
                where: {
                    "id": judge_id
                },
                limit: 1
            },
            success: function(response) {
                // companyField.value = response[0].company_name
                document.getElementById("inputEmployeId").value = response[0].employee_id
            },
            error: function(xhr, status, error) {
                // Tangani kesalahan jika ada
                console.error(xhr.responseText);
            }
        });
        //link untuk update
        var form = document.getElementById('updateStatusJuri');
        var url = `{{ route('management-system.revoke.juri', ['id' => ':judge_id']) }}`;
        url = url.replace(':judge_id', judge_id);
        form.action = url;
    }
    function update_judge(judge_id){
         $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET', // Metode HTTP POST
            url: '{{ route('query.custom') }}',
            dataType: 'json',
            data: {
                table: "judges",
                where: {
                    "id": judge_id
                },
                limit: 1
            },
            success: function(response) {
                // companyField.value = response[0].company_name
                document.getElementById("inputEmployeId").value = response[0].employee_id
            },
            error: function(xhr, status, error) {
                // Tangani kesalahan jika ada
                console.error(xhr.responseText);
            }
        });
        //link untuk update
        var form = document.getElementById('updateStatusJuri');
        var url = `{{ route('management-system.update.juri', ['id' => ':judge_id']) }}`;
        url = url.replace(':judge_id', judge_id);
        form.action = url;
    }
</script>
@endpush
