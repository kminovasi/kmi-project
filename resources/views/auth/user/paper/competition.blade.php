@extends('layouts.app')
@section('title', 'Pendaftaran Event Group')
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
                <div class="mb-3 d-flex align-items-center">
                    <button id="openFilterModal" class="btn btn-outline-secondary me-2" data-bs-toggle="modal"
                        data-bs-target="#filterModal">
                        <i class="fas fa-filter"></i> Filter Data
                    </button>

                    <span id="selectedFilterDisplay" class="text-muted"></span>
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

    <!-- Modal Filter Tahun dan Perusahaan -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="yearSelect" class="form-label">Pilih Tahun</label>
                        <select id="yearSelect" class="form-select">
                            <option value="">Semua Tahun</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = 2020; // Sesuaikan dengan tahun awal data Anda
                            @endphp
                            @for ($year = $currentYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                    </div>

                    @if (Auth::user()->role === 'Superadmin')
                        <div class="mb-3">
                            <label for="companySelect" class="form-label">Pilih Perusahaan</label>
                            <select id="companySelect" class="form-select">
                                <option value="">Semua Perusahaan</option>
                                @foreach (\App\Models\Company::all() as $company)
                                    <option value="{{ $company->company_code }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="applyFilter">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="">
    $(document).ready(function() {
            // Constants
            const DATATABLE_CONFIG = {
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('group-event.getAllPaper') }}",
                    type: 'GET',
                    data: function(d) {
                        d.year = $('#yearSelect').val(); // Tambahkan parameter tahun
                        @if(Auth::user()->role === 'Superadmin')
                            d.company = $('#companySelect').val();
                        @endif
                        return d;
                    }
                },
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
                        title: 'Event AP',  // Ubah judul kolom
                        data: 'ap_events',  // Gunakan nama data baru
                        name: 'ap_events',
                        orderable: false,
                        searchable: false
                    },
                    {
                        title: 'Event Internal',  // Ubah judul kolom
                        data: 'internal_events',  // Gunakan nama data baru
                        name: 'internal_events',
                        orderable: false,
                        searchable: false
                    },
                    {
                        title: 'Event Group',
                        data: 'group_events',
                        name: 'group_events',
                        orderable: false,
                        searchable: false
                    }
                ],
                responsive: true,
                columnDefs: [
                    {
                        // Atur lebar kolom baru (opsional)
                        targets: 5,  // indeks kolom Event Group
                        width: '15%'
                    }
                ]
            };
            const SWAL_CONFIG = {
                warning: {
                    icon: 'warning',
                    title: 'Peringatan',
                    confirmButtonColor: '#3085d6'
                },
                confirmation: {
                    title: 'Konfirmasi',
                    text: "Apakah Anda yakin ingin menugaskan team yang dipilih ke event ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Tugaskan!',
                    cancelButtonText: 'Batal'
                }
            };

            // Initialize DataTable
            const table = $('#datatable-competition').DataTable(DATATABLE_CONFIG);

            // Helper Functions
            function updateSelectedCount() {
                const count = $('.paper_checkbox:checked').length;
                $('#selected-count').text(`${count} team(s) selected`);
            }

            function showLoading(show = true) {
                $('#loading-overlay')[show ? 'fadeIn' : 'fadeOut']();
                $('#assign-to-event').prop('disabled', show);
            }

            function resetSelection() {
                $('.paper_checkbox, #select-all').prop('checked', false);
                updateSelectedCount();
            }

            function handleAssignmentResponse(response) {
                const config = response.success ?
                    { icon: 'success', title: 'Berhasil!', text: 'Tim berhasil ditugaskan ke event' } :
                    { icon: 'error', title: 'Oops...', text: `Error: ${response.message}` };

                Swal.fire({
                    ...config,
                    confirmButtonColor: '#3085d6'
                });

                if (response.success) {
                    table.ajax.reload();
                }
            }

            function validateSelection() {
                const selectedIds = $('.paper_checkbox:checked').map(function() {
                    return $(this).val();
                }).get();

                const eventId = $('#event-select').val();

                if (selectedIds.length === 0) {
                    Swal.fire({
                        ...SWAL_CONFIG.warning,
                        text: 'Silakan pilih minimal satu team'
                    });
                    return null;
                }

                if (!eventId) {
                    Swal.fire({
                        ...SWAL_CONFIG.warning,
                        text: 'Silakan pilih event'
                    });
                    return null;
                }

                return { selectedIds, eventId };
            }

            async function assignTeams(data) {
                try {
                    showLoading(true);
                    const response = await $.ajax({
                        url: "{{ route('group-event.assignTeams') }}",
                        type: 'POST',
                        data: {
                            team_ids: data.selectedIds,
                            event_id: data.eventId,
                            _token: '{{ csrf_token() }}'
                        }
                    });
                    handleAssignmentResponse(response);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Terjadi kesalahan saat menugaskan tim',
                        confirmButtonColor: '#3085d6'
                    });
                } finally {
                    showLoading(false);
                    resetSelection();
                }
            }

            // Event Handlers
            $('#select-all').on('click', function() {
                const rows = table.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
                updateSelectedCount();
            });

            $('#datatable-competition tbody').on('change', 'input[type="checkbox"]', function() {
                if (!this.checked) {
                    const el = $('#select-all').get(0);
                    if (el?.checked) {
                        el.indeterminate = true;
                    }
                }
                updateSelectedCount();
            });

            $('#assign-to-event').on('click', function() {
                const validationResult = validateSelection();
                if (!validationResult) return;

                Swal.fire(SWAL_CONFIG.confirmation).then((result) => {
                    if (result.isConfirmed) {
                        assignTeams(validationResult);
                    }
                });
            });

            $('#applyFilter').on('click', function() {
                const selectedYear = $('#yearSelect').val();

                @if(Auth::user()->role === 'Superadmin')
                const selectedCompany = $('#companySelect').val();
                @endif

                // Update tampilan filter yang dipilih
                let filterDisplay = [];
                if (selectedYear) {
                    filterDisplay.push(`Tahun: ${selectedYear}`);
                }

                @if(Auth::user()->role === 'Superadmin')
                if (selectedCompany) {
                    const companyName = $(`#companySelect option[value="${selectedCompany}"]`).text();
                    filterDisplay.push(`Perusahaan: ${companyName}`);
                }
                @endif

                $('#selectedFilterDisplay').text(filterDisplay.join(' | '));

                // Reload tabel dengan filter
                table.ajax.reload();

                // Tutup modal
                $('#filterModal').modal('hide');
            });
        });
    </script>
@endpush
