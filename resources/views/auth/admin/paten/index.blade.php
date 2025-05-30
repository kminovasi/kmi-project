@extends('layouts.app')
@section('title', 'Paten Inovasi')
@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
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
                            Paten Inovasi
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
                @if(Auth::user()->role == 'Superadmin' || Auth::user()->role == 'admin')
                <div class="btn-container mb-3 text-end">
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#patent-application">Buat Usulan Paten</button>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#document-template">Perbarui Template Dukomen</button>
                </div>
                @endif
                <x-patent.patent-table />
            </div>
        </div>
    </div>

    {{-- Modal Patent Upload Template --}}
    <div class="modal" id="document-template" tabindex="-1" aria-labelledby="document-template" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Perbaruan Template Dokumen Paten</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('patent.updateTemplateDocument') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="draft_paten" class="form-label">Draft Patent</label>
                            <input type="file" class="form-control" id="draft_paten" name="draft_paten">
                        </div>
                        <div class="mb-3">
                            <label for="ownership_letter" class="form-label">Surat Kepemilikan</label>
                            <input type="file" class="form-control" id="ownership_letter" name="ownership_letter">
                        </div>
                        <div class="mb-3">
                            <label for="statement_of_transfer_rights" class="form-label">Surat Pengalihan Hak</label>
                            <input type="file" class="form-control" id="statement_of_transfer_rights" name="statement_of_transfer_rights">
                        </div>
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Patent Application --}}
    <div class="modal" id="patent-application" tabindex="-1" aria-labelledby="patent-application" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Pengajuan Paten Inovasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('patent.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <input type="text" id="inputInnovationTittle" class="form-control" placeholder="Masukkan Judul Inovasi" autocomplete="off">
                            <input type="hidden" name="title_id" id="inputInnovationTittleId">

                            <div id="suggestions-title" class="list-group position-absolute z-3 bg-white w-100" style="display: none;"></div>
                        </div>
                        <div class="mb-3">
                            <select name="pic_id" id="selectPIC" class="form-select">
                                <option value="">Pilih PIC</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="status" class="form-select ms-0" aria-label="Default select example">
                                <option selected>Pilih Status Pengajuan</option>
                                <option value="Belum Diajukan">Belum Diajukan</option>
                                <option value="Pengajuan">Pengajuan</option>
                                <option value="Dikaji DJKI">Dikaji DJKI</option>
                                <option value="Paten">Paten</option>
                            </select>
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
            $('#inputInnovationTittleId').val(ui.item.id);

            // Load PIC setelah judul dipilih
            $.ajax({
                url: "{{ route('patent.picSuggestion') }}",
                type: "GET",
                data: { title_id: ui.item.id },
                success: function(data) {
                    let select = $('#selectPIC');
                    select.empty().append('<option value="">Pilih PIC</option>');

                    if (data.length === 0) {
                        select.append('<option value="" disabled>Tidak ada PIC tersedia</option>');
                    } else {
                        data.forEach(function(user) {
                            select.append(`<option value="${user.id}">${user.name}</option>`);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading PIC:', error);
                    alert('Gagal memuat data PIC. Silakan coba lagi.');
                }
            });
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
