@extends('layouts.app')

@section('title', 'Daftar Coaching Clinic | Dashboard')

@push('css')
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
@endpush

@section('content')
<x-header-content title="Daftar Coaching Clinic"></x-header-content>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <table id="eventsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">PIC</th>
                            <th class="text-center">Nama Team</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Tanggal Choaching</th>
                            <th class="text-center">Evidence</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($coachingClinics as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $item->user->name }}</td>
                                <td class="text-center">{{ $item->team->team_name }}</td>
                                <td class="text-center">
                                    @if ($item->status === 'accept')
                                    <span class="badge bg-success">Diterima</span>
                                    @elseif ($item->status === 'reject')
                                    <span class="badge bg-danger">Ditolak</span>
                                    @elseif ($item->status === 'pending')
                                    <span class="badge bg-secondary">Menunggu</span>
                                    @else
                                    <span class="badge bg-primary">Finish</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ \Carbon\Carbon::parse($item->coaching_date)->format('d M Y') }}</td>
                                <td class="text-center">-</td>
                                <td class="d-flex flex-row gap-1 flex-wrap justify-content-center">
                                    @if($item->status === 'pending')
                                    <button class="btn btn-sm btn-primary approve-btn" 
                                        data-coaching-id="{{ $item->id }}"
                                        data-coaching-status="accept">
                                        Terima
                                    </button>
                                    <button class="btn btn-sm btn-danger approve-btn" 
                                        data-coaching-id="{{ $item->id }}"
                                        data-coaching-status="reject">
                                        Tolak
                                    </button>
                                    @endif
                                    <a href="" class="btn btn-sm btn-success"
                                        data-name="{{ $item->user->name }}"
                                        data-company="{{ $item->company->company_name }}"
                                        data-coaching-date="{{ $item->coaching_date }}"
                                        data-phone-number="{{ $item->team->phone_number }}"
                                        data-team-name="{{ $item->team->team_name }}">
                                        Whatsapp
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<!-- Modal Edit Status -->
<div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Status Paten</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="acceptModalForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3 form-body">
                        
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

            // Klik tombol Edit Status
            $('.approve-btn').click(function() {
                let coachingId = $(this).data('coaching-id');
                let status = $(this).data('coaching-status');

                $('#acceptModalForm').attr('action', '/coaching-clinic/update-coaching-apply/' + coachingId + '/' + status);

                const containerBody = document.querySelector('.form-body');
                containerBody.innerHTML = ''; // perbaikan disini

                if (status === 'accept') {
                    containerBody.innerHTML = `
                        <label for="coaching-date" class="form-label">Tanggal Coaching</label>
                        <input class="form-control form-control-sm coaching_date" type="date" name="coaching_date" id="coaching-date">
                    `;
                } else if (status === 'reject') {
                    containerBody.innerHTML = `
                        <label for="reason" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control form-control-sm" name="reason" id="reason" rows="3"></textarea>
                    `;
                }

                $('#acceptModal').modal('show');
            });
        });
    </script>
@endpush
