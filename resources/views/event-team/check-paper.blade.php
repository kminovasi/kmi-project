@extends('layouts.app')
@section('title', 'Check Paper')
@push('css')
    <style>
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            /* Background gelap dengan transparansi */
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            /* Warna teks putih untuk kontras */
            font-size: 1.5rem;
            /* Ukuran font lebih besar untuk visibilitas */
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
                            <div class="page-header-icon"><i data-feather="edit"></i></div>
                            Paper Review
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('event-team.show', $eventId) }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid py-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header text-white" style="background-color: #e94838;">
                        <h3 class="card-title mb-0 text-white">Paper Review</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Team Name</label>
                                    <p class="form-control-plaintext">{{ $paper->team->team_name }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Innovation Title</label>
                                    <p class="form-control-plaintext">{{ $paper->innovation_title }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Lokasi Inovasi</label>
                                    <p class="form-control-plaintext">{{ $paper->inovasi_lokasi }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Abstract</label>
                                    <div class="border rounded p-3">
                                        {!! $paper->abstract !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Problem</label>
                                    <div class="border rounded p-3">
                                        {!! $paper->problem !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Main Cause</label>
                                    <div class="border rounded p-3">
                                        {!! $paper->main_cause !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Solution</label>
                                    <div class="border rounded p-3">
                                        {!! $paper->solution !!}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Financial</label>
                                    <p class="form-control-plaintext">Rp {{ $paper->financial_formatted }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Potential Benefit</label>
                                    <p class="form-control-plaintext">Rp {{ $paper->potential_benefit_formatted }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Non Financial</label>
                                    <div class="border rounded p-3">
                                        {!! $paper->non_financial !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Potensi Replikasi</label>
                                    <div class="border rounded p-3">
                                        {!! $paper->potensi_replikasi !!}
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Full Paper</label>
                                    @if ($paper->full_paper && !str_contains($paper->full_paper, '/AP/'))
                                        <div>
                                            <a href="{{ asset('storage/' . $paper->full_paper) }}"
                                                class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-file-pdf"></i> View Full Paper
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-muted">Paper belum diupload</p>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">File Review</label>
                                    @if ($paper->file_review)
                                        <div>
                                            <a href="{{ asset('storage/' . $paper->file_review) }}"
                                                class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-file-pdf"></i> View Review File
                                            </a>
                                        </div>
                                    @else
                                        <p class="text-muted">No file uploaded</p>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Innovation Photo</label>
                                    @if ($paper->innovation_photo)
                                        <div>
                                            <img src="{{ asset('storage/' . $paper->innovation_photo) }}"
                                                class="img-fluid rounded" alt="Innovation Photo">
                                        </div>
                                    @else
                                        <p class="text-muted">No image uploaded</p>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Proof of Idea</label>
                                    @if ($paper->proof_idea)
                                        <div>
                                            <img src="{{ asset('storage/' . $paper->proof_idea) }}"
                                                class="img-fluid rounded" alt="Proof of Idea">
                                        </div>
                                    @else
                                        <p class="text-muted">No image uploaded</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <form id="updateStatusForm"
                                    action="{{ route('event-team.updatePaperStatus', ['id' => $paper->id, 'eventId' => $eventId]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Update Status</label>
                                        <select name="status_event" id="statusSelect" class="form-select">
                                            <option value="accept_group"
                                                {{ $paper->status_event == 'accept_group' ? 'selected' : '' }}>Accept Group
                                            </option>
                                            <option value="reject_group"
                                                {{ $paper->status_event == 'reject_group' ? 'selected' : '' }}>Reject Group
                                            </option>
                                            <option value="accept_national"
                                                {{ $paper->status_event == 'accept_national' ? 'selected' : '' }}>Accept
                                                National</option>
                                            <option value="reject_national"
                                                {{ $paper->status_event == 'reject_national' ? 'selected' : '' }}>Reject
                                                National</option>
                                            <option value="accept_international"
                                                {{ $paper->status_event == 'accept_international' ? 'selected' : '' }}>
                                                Accept International</option>
                                            <option value="reject_international"
                                                {{ $paper->status_event == 'reject_international' ? 'selected' : '' }}>
                                                Reject International</option>
                                        </select>
                                    </div>
                                    <button type="submit" id="updateStatusBtn" class="btn btn-primary">Update
                                        Status</button>
                                    <div class="spinner-border text-primary" id="loadingSpinner" style="display: none;"
                                        role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rejection Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm"
                    action="{{ route('event-team.updatePaperStatus', ['id' => $paper->id, 'eventId' => $eventId]) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" name="status_event" id="reject_status_event">
                        <div class="mb-3">
                            <label class="form-label">Comments</label>
                            <textarea name="comments" class="form-control" rows="4" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="loadingOverlay" style="display: none;">
        <div class="d-flex justify-content-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-white">Loading...</p>
        </div>
    </div>
    @push('js')
        <script>
            $(document).ready(function() {
                function checkStatus() {
                    const status = $('#statusSelect').val();
                    return status.includes('reject');
                }

                function toggleRejectModal(show) {
                    if (show) {
                        var myModal = new bootstrap.Modal(document.getElementById('rejectModal'));
                        myModal.show();
                    } else {
                        $('#rejectModal').modal('hide');
                    }
                }

                // Check initial status
                if (checkStatus()) {
                    $('#updateStatusBtn').text('Reject (Requires Comment)');
                }

                $('#statusSelect').on('change', function() {
                    if (checkStatus()) {
                        $('#updateStatusBtn').text('Reject (Requires Comment)');
                    } else {
                        $('#updateStatusBtn').text('Update Status');
                    }
                });

                $('#updateStatusForm').on('submit', function(e) {
                    $('#loadingOverlay').show();
                    if (checkStatus()) {
                        e.preventDefault();
                        $('#reject_status_event').val($('#statusSelect').val());
                        toggleRejectModal(true);
                    }
                });

                $('#rejectForm').on('submit', function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: $(this).attr('action'),
                        method: 'POST',
                        data: $(this).serialize(),
                        beforeSend: function() {
                            $('#loadingOverlay').show(); // Tampilkan overlay sebelum mengirim
                        },
                        success: function(response) {
                            window.location.reload();
                        },
                        error: function(xhr) {
                            alert('Error updating status');
                        },
                        complete: function() {
                            $('#loadingOverlay').hide(); // Sembunyikan overlay setelah selesai
                        }
                    });
                });

                // Close modal and reset form when modal is dismissed
                $('#rejectModal').on('hidden.bs.modal', function() {
                    $('#rejectForm')[0].reset();
                });
            });
        </script>
    @endpush
@endsection
