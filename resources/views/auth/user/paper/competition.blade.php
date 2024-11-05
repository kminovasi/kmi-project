@extends('layouts.app')
@section('title', 'Data Paper')
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style type="text/css">
        .step-one h1 {
            text-align: center;
        }

        .step-one img {
            width: 75%;
            height: 75%;
        }

        .step-one p {
            text-align: justify;
        }

        .file-review {
            margin: 20px 10px;
        }

        .active-link {
            color: #ffc004;
            background-color: #e81500;
        }


        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .loading-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
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
                            Data Paper - Innovation Paper
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        @include('auth.user.paper.navbar')
        <div class="mb-3">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                    {{ session('success') }}
                    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <select id="event-select" class="form-select" style="width: 200px; display: inline-block;">
                        <option value="">Select Event</option>
                        @foreach ($data_event as $event)
                            <option value="{{ $event->id }}">{{ $event->event_name }}</option>
                        @endforeach
                    </select>
                    <button id="assign-to-event" class="btn btn-primary">Assign to Event</button>
                    <span id="selected-count" class="ms-3">0 team(s) selected</span>
                </div>
                <table id="datatable-competition">
                </table>
            </div>

        </div>
    </div>

    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-content">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="mt-2">Sedang memproses...</h5>
            <p>Mohon tunggu sebentar</p>
        </div>
    </div>


@endsection

@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="">
 // Add this script to your existing JavaScript
$(document).ready(function() {
  let table = $('#datatable-competition').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('group-event.getAllPaper') }}",
    columns: [
        {
            title: '<input type="checkbox" name="select_all" value="1" id="select-all">',
            data: 'checkbox',
            name: 'checkbox',
            orderable: false,
            searchable: false,
            width: '5%'
        },
        {
            title: 'No',
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false,
            width: '5%'
        },
        {
            title: 'Team',
            data: 'team_name',
            name: 'teams.team_name'
        },
        {
            title: 'Perusahaan',
            data: 'company_name',
            name: 'companies.company_name'
        },
        {
            title: 'Judul Inovasi',
            data: 'innovation_title',
            name: 'papers.innovation_title'
        },
        {
            title: 'Event yang diikuti',
            data: 'registered_events',
            name: 'registered_events',
            orderable: false,
            searchable: false
        }
    ],
    responsive: true
});

    // Handle click on "Select all" control
    $('#select-all').on('click', function(){
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
        updateSelectedCount();
    });

    // Handle click on checkbox
    $('#datatable-competition tbody').on('change', 'input[type="checkbox"]', function(){
        if(!this.checked){
            var el = $('#select-all').get(0);
            if(el && el.checked && ('indeterminate' in el)){
                el.indeterminate = true;
            }
        }
        updateSelectedCount();
    });

    // Function to update selected count
    function updateSelectedCount() {
        var count = $('.paper_checkbox:checked').length;
        $('#selected-count').text(count + ' team(s) selected');
    }

    // Handle assign to event
  // Handle assign to event
 $('#assign-to-event').click(function(){
            var selectedIds = [];
            $('.paper_checkbox:checked').each(function(){
                selectedIds.push($(this).val());
            });

            if(selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih minimal satu team',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            var eventId = $('#event-select').val();
            if(!eventId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Silakan pilih event',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Konfirmasi sebelum assign
            Swal.fire({
                title: 'Konfirmasi',
                text: "Apakah Anda yakin ingin menugaskan team yang dipilih ke event ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Tugaskan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading overlay
                    $('#loading-overlay').fadeIn();

                    // Disable the assign button
                    $('#assign-to-event').prop('disabled', true);

                    $.ajax({
                        url: "{{ route('group-event.assignTeams') }}",
                        type: 'POST',
                        data: {
                            team_ids: selectedIds,
                            event_id: eventId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if(response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Tim berhasil ditugaskan ke event',
                                    confirmButtonColor: '#3085d6'
                                });
                                table.ajax.reload();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Error: ' + response.message,
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan saat menugaskan tim',
                                confirmButtonColor: '#3085d6'
                            });
                        },
                        complete: function() {
                            // Hide loading overlay
                            $('#loading-overlay').fadeOut();

                            // Re-enable the assign button
                            $('#assign-to-event').prop('disabled', false);

                            // Clear checkboxes
                            $('.paper_checkbox').prop('checked', false);
                            $('#select-all').prop('checked', false);
                            updateSelectedCount();
                        }
                    });
                }
            });
        });
    });


</script>
@endpush
