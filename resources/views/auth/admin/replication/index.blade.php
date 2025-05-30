@extends('layouts.app')
@section('title', 'Replikasi Inovasi')
@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<style>
    .ui-autocomplete {
        z-index: 999999 !important;
        max-width: 45rem !important;
        overflow-x: hidden !important;
    }
    .ui-autocomplete li {
        border-bottom: 1px solid #4d4d4d !important;
    }
    .search-input {
        height: 2rem;
        width: 27.5%;
    }
</style>
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Replikasi Inovasi
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div id="alertContainer"></div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-end mb-2">
                    <input type="text" id="search" class="form-control search-input" placeholder="Cari Daftar Paten">
                </div>
                <div class="btn-container mb-3 text-end">
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#replication-application">Buat Usulan Replikasi</button>
                </div>
                <x-replication.replication-table />
            </div>
        </div>
    </div>

    {{-- Modal Replication Application --}}
    <div class="modal" id="replication-application" tabindex="-1" aria-labelledby="replication-application" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Pengajuan Replikasi Inovasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('replication.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" id="inputInnovationTittle" class="form-control" placeholder="Masukkan Judul Inovasi" autocomplete="off">
                            <input type="hidden" name="title_id" id="inputInnovationTittleId">
                        </div>
                        <div class="mb-3">
                            @if(Auth::user()->role !== 'Superadmin' || Auth::user()->role !== 'Admin')
                            <input type="text" id="inputPIC" class="form-control" placeholder="Masukkan Nama" autocomplete="off" value="{{ Auth::user()->name }}" readonly>
                            <input type="hidden" name="pic_id" value="{{ Auth::user()->id }}">
                            @else
                            <input type="text" id="inputPIC" class="form-control" placeholder="Masukkan Nama" autocomplete="off">
                            <input type="hidden" name="pic_id" id="inputEmployeeId">
                            @endif
                        </div>
                        <div class="mb-3">
                            @if(Auth::user()->role !== 'Superadmin' || Auth::user()->role !== 'Admin')
                            <input type="text" id="InputCompanyName" class="form-control" placeholder="Masukkan Perusahaan" autocomplete="off" value="{{ Auth::user()->company_name }}" readonly>
                            <input type="hidden" name="company_code" value="{{ Auth::user()->company_code }}">
                            @else
                            <input type="text" id="InputCompanyName" class="form-control" placeholder="Masukkan Perusahaan" autocomplete="off">
                            <input type="hidden" name="company_code" id="inputCompanyId">
                            @endif
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script>
    $("#inputInnovationTittle").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('patent.tittleSuggestion') }}",
                type: "GET",
                dataType: "json",
                success: function(data) {
                    let result = $.map(data, function(item) {
                        return {
                            label: item.innovation_title,
                            value: item.innovation_title,
                            id: item.id
                        };
                    });
                    response($.ui.autocomplete.filter(result, request.term));
                }
            });
        },
        select: function(event, ui) {
            $("#inputInnovationTittleId").val(ui.item.id);
        }
    });
    $("#inputPIC").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('replication.userSuggestion') }}",
                type: "GET",
                dataType: "json",
                data: {
                    query: request.term
                },
                success: function(data) {
                    let result = $.map(data, function(item) {
                        return {
                            label: item.employee_id + ' - ' + item.name,
                            value: item.name,
                            id: item.id,
                            company_code: item.company_code,
                            company_name: item.company_name
                        };
                    });
                    response(result); // langsung kirim data tanpa filter ulang
                },
            });
        },
        select: function(event, ui) {
            $("#inputEmployeeId").val(ui.item.id);
            $("#InputCompanyName").val(ui.item.company_name);
            $("#inputCompanyId").val(ui.item.company_code);
        }
    });
    function showAlert(message, type = 'success') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        $('#alertContainer').html(alertHtml);
    }
</script>

{{-- Search --}}
<script>
    let debounceTimeout;
    
    // Ambil elemen search
    const searchInput = document.getElementById('search');

    searchInput.addEventListener('input', function() {
        const query = this.value;

        // Clear timeout sebelumnya
        clearTimeout(debounceTimeout);

        // Set timeout untuk request setelah 500ms
        debounceTimeout = setTimeout(function() {
            fetch(`{{ route('patent.search') }}?q=${query}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Ajax request
                }
            })
            .then(response => response.text()) // Ambil response HTML
            .then(data => {
                // Update konten tabel
                document.getElementById('patent-table-container').innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
        }, 500); // Tunggu 500ms setelah pengguna berhenti mengetik
    });
</script>

@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlert(@json(session('success')), 'success');
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlert(@json(session('error')), 'danger');
        });
    </script>
@endif

@endpush
