@extends('layouts.app')
@section('title', 'List Team | ' . $event->event_name)

@section('content')
    @push('css')
        <link
            href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    @endpush
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            List Team | Event {{ $event->event_name }}
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-xl px-4 ">
        <div class="card mb-4">
            <div class="card-header text-white" style="background-color: #e94838;">List of Teams</div>
            <div class="card-body">
                <table id="datatablesSimple" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>Innovation Title</th>
                            <th>Company Name</th>
                            @if (Auth::user()->role === 'Superadmin')
                                <th>Status Lolos</th>
                                <th>Status Full Paper</th>
                            @endif
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated by DataTables -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/datatables.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            .team-row-member {
                background-color: rgba(0, 123, 255, 0.1) !important;
            }

            .team-row-leader {
                background-color: rgba(40, 167, 69, 0.1) !important;
            }

            .team-row-facilitator {
                background-color: rgba(255, 193, 7, 0.1) !important;
            }

            .team-row-gm {
                background-color: rgba(108, 117, 125, 0.1) !important;
            }

            .team-badge {
                padding: 2px 6px;
                border-radius: 3px;
                font-size: 0.8em;
                margin-left: 5px;
                font-weight: bold;
            }

            .badge-member {
                background-color: #007bff;
                color: white;
            }

            .badge-leader {
                background-color: #28a745;
                color: white;
            }

            .badge-facilitator {
                background-color: #ffc107;
                color: black;
            }

            .badge-gm {
                background-color: #6c757d;
                color: white;
            }

            .btn-group .btn {
                margin-right: 4px;
            }

            .btn-group .btn:last-child {
                margin-right: 0;
            }

            .btn-group {
                display: flex;
                gap: 4px;
                flex-wrap: wrap;
            }
        </style>
    @endpush

    @push('js')
        <script
            src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
        </script>
        <script>
            $(document).ready(function() {
                var columns = [{
                        data: 'team_name',
                        render: function(data, type, row) {
                            let html = data;
                            if (row.is_user_team) {
                                const roleClasses = {
                                    'member': 'badge-member',
                                    'leader': 'badge-leader',
                                    'facilitator': 'badge-facilitator',
                                    'gm': 'badge-gm'
                                };
                                const roleLabels = {
                                    'member': 'Member',
                                    'leader': 'Leader',
                                    'facilitator': 'Facilitator',
                                    'gm': 'GM'
                                };
                                html +=
                                    `<span class="team-badge ${roleClasses[row.user_role]}">${roleLabels[row.user_role]}</span>`;
                            }
                            return html;
                        }
                    },
                    {
                        data: 'innovation_title',
                        name: 'innovation_title'
                    },
                    {
                        data: 'company_name',
                        name: 'company_name'
                    }
                ];

                // Add status column if user is superadmin
                @if (Auth::user()->role === 'Superadmin')
                    columns.push({
                        data: 'status_lolos',
                        render: function(data, type, row) {
                            return data ?
                                '<span class="badge bg-success">Masuk Grup</span>' :
                                '<span class="badge bg-danger">Reject</span>';
                        }
                    });
                @endif
                @if (Auth::user()->role === 'Superadmin')
                    columns.push({
                        data: 'has_full_paper',
                        render: function(data, type, row) {
                            return data ?
                                '<span class="badge bg-success">Sudah Upload</span>' :
                                '<span class="badge bg-danger">Belum Upload</span>';
                        }
                    });
                @endif

                columns.push({
                    data: null,
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let buttons = `<a href="${row.view_url}" class="btn btn-primary btn-sm">View</a>`;

                        if (row.user_role === 'leader' || row.user_role === 'member' && row.has_paper) {
                            buttons += ` <a href="${row.edit_url}"
                        class="btn btn-warning btn-sm ms-1">
                        <i class="fas fa-edit"></i> Edit Paper
                    </a>`;
                            buttons += `<a href="${row.edit_benefit_url}"
                        class="btn btn-info btn-sm ms-1"
                        data-bs-toggle="tooltip" title="Edit Team Benefits">
                        <i class="fas fa-chart-line"></i> Edit Benefit
                    </a>`;
                        }
                        if (row.role === 'Superadmin') {
                            buttons += `<a href="${row.check_paper}"
                        class="btn btn-teal btn-sm"
                        data-bs-toggle="tooltip" title="Check Team">
                        <i class="fa-solid fa-magnifying-glass"></i>Check
                    </a>`;
                        }

                        return `<div class="btn-group" role="group">${buttons}</div>`;
                    }
                });

                $('#datatablesSimple').DataTable({
                    processing: true,
                    serverSide: false,
                    responsive: true,
                    ajax: "{{ route('event-team.buildPaperQueryByEvent', $event->id) }}",
                    columns: columns,
                    createdRow: function(row, data, dataIndex) {
                        if (data.is_user_team) {
                            $(row).addClass(`team-row-${data.user_role}`);
                        }
                    }
                });
            });
        </script>
    @endpush
@endsection
