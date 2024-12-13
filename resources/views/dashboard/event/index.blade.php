@extends('layouts.app')

@section('title', 'Daftar Event')

@push('css')
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
@endpush

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Daftar Event</h1>
        <div class="card">
            <div class="card-body">
                <table id="eventsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Event</th>
                            <th>Status</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Berakhir</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $event->event_name }}</td>
                                <td>
                                    @if ($event->status === 'active' && now()->between($event->date_start, $event->date_end))
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($event->date_start)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($event->date_end)->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('dashboard-event.show', $event->id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Lihat Deskripsi
                                    </a>
                                    <a href="{{ route('dashboard-event.statistics', $event->id) }}"
                                        class="btn btn-sm btn-info">
                                        <i class="bi bi-bar-chart"></i> Statistik
                                    </a>
                                </td>
                            </tr>
                        @endforeach
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

    <script>
        $(document).ready(function() {
            $('#eventsTable').DataTable({
                responsive: true,
                columnDefs: [{
                        orderable: false,
                        targets: [5]
                    } // Kolom Action tidak bisa diurutkan
                ]
            });
        });
    </script>
@endpush
