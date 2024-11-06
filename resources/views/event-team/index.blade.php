@extends('layouts.app')
@section('title', 'Event')
@push('css')
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
    <style>
        .badge {
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }

        /* Table hover effect */
        #eventsTable tbody tr {
            transition: background-color 0.2s ease;
        }

        #eventsTable tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
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
                            List Event
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-xl px-4 mt-4">
        <div class="card">
            <div class="card-header">Events</div>
            <div class="card-body">
                <form id="filterForm" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <select name="type" class="form-select">
                                <option value="">Select Type</option>
                                <option value="AP">Anak Perusahaan</option>
                                <option value="group">Group</option>
                                <option value="national">National</option>
                                <option value="international">International</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="company_code" class="form-select">
                                <option value="">Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->company_code }}">{{ $company->company_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-select">
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value ="not active">Not Active</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Filter</button>
                </form>

                <table id="eventsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Company</th>
                            <th>Mulai</th>
                            <th>Berakhir</th>
                            <th>Status</th>
                            <th>Year</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script
        src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#eventsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: '{{ route('event-team.getEvents') }}',
                    data: function(d) {
                        d.type = $('select[name=type]').val();
                        d.company_code = $('select[name=company_code]').val();
                        d.status = $('select[name=status]').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables error:', error);
                    }
                },
                columns: [{
                        data: 'event_name',
                        name: 'event_name'
                    },
                    {
                        data: 'company',
                        name: 'company'
                    },
                    {
                        data: 'date_start',
                        name: 'date_start',
                        render: function(data, type, row) {
                            return moment(data).format('DD MMM YYYY');
                        }
                    },
                    {
                        data: 'date_end',
                        name: 'date_end',
                        render: function(data, type, row) {
                            return moment(data).format('DD MMM YYYY');
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            if (data === 'active') {
                                return '<span class="badge badge-success">Active</span>';
                            } else {
                                return '<span class="badge badge-danger">Not Active</span>';
                            }
                        }
                    },
                    {
                        data: 'year',
                        name: 'year'
                    },
                    {
                        data: 'id',
                        name: 'id',
                        visible: false
                    }
                ],
                createdRow: function(row, data, dataIndex) {
                    // Create a detailed tooltip content
                    var tooltipContent = `
                <strong>Lihat list team</strong><br>
            `;

                    // Apply the tooltip with custom options
                    $(row).attr({
                        'data-bs-toggle': 'tooltip',
                        'data-bs-html': 'true',
                        'data-bs-title': tooltipContent,
                        'data-bs-placement': 'right',
                        'role': 'button',
                        'style': 'cursor: pointer'
                    });
                }
            });

            // Initialize Bootstrap tooltips with custom options
            var tooltipOptions = {
                trigger: 'hover',
                html: true,
                animation: true,
                delay: {
                    show: 200,
                    hide: 100
                },
                template: `
            <div class="tooltip" role="tooltip">
                <div class="tooltip-arrow"></div>
                <div class="tooltip-inner p-3" style="max-width: 300px; text-align: left;"></div>
            </div>
        `
            };

            function initTooltips() {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl, tooltipOptions);
                });
            }

            // Initialize tooltips
            initTooltips();

            // Re-initialize tooltips after table updates
            table.on('draw', function() {
                // Destroy existing tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll(
                    '[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                    var tooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                    if (tooltip) {
                        tooltip.dispose();
                    }
                });
                // Initialize new tooltips
                initTooltips();
            });

            // Handle row click
            $('#eventsTable tbody').on('click', 'tr', function() {
                var data = table.row(this).data();
                if (data) {
                    window.location.href = '{{ url('event') }}/' + data.id;
                }
            });

            // Handle filter form submission
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                table.draw();
            });
        });
    </script>
@endpush
