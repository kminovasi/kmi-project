@extends('layouts.app')
@section('title', 'Paper Detail')

<style>
    .timeline-wrapper {
        position: relative;
        padding: 2rem 1rem;
    }

    .timeline-line {
        position: absolute;
        top: 2rem; /* posisi vertikal garis (sesuaikan dengan circle) */
        left: 0;
        right: 0;
        height: 2px;
        background-color: #ccc;
        z-index: 1;
    }

    .timeline-item {
        position: relative;
        z-index: 2;
    }

    .timeline-circle {
        width: 3.5rem;
        height: 3.5rem;
        line-height: 3.5rem;
        border-radius: 50%;
        color: white;
        text-align: center;
        margin: auto;
    }

    .information {
        width: 100%;
        text-align: center;
        border-top: 1px solid #ccc;
        padding-top: 1rem;
    }
</style>

@section('content')
    <div class="container mt-4">
        <div class="row d-flex justify-content-center">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Informasi Status Paten Inovasi</div>
                    <div class="card-body">
                        <h6 class="fw-500">Status Paten</h6>
                        <div class="card bg-success text-white" style="height: 2rem">
                            <p class="align-middle text-center text-capitalize mt-1">{{ $patent->application_status }}</p>
                        </div>
                        
                        @if($patent->application_status == 'Paten')
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-4 p-3 mt-3 border rounded">
                            @for ($year = $startYear; $year <= $startYear + 9; $year++)
                                <div class="text-center m-2" style="width: 10%;">
                                    <div class="timeline-circle"
                                        style="background-color: {{ in_array($year, $paidYears) ? 'green' : '#ccc' }};">
                                        {{ $year }}
                                    </div>
                                    @if (isset($paymentDates[$year]))
                                        @foreach ($paymentDates[$year] as $date)
                                            <small class="d-block mt-1">
                                                {{ \Carbon\Carbon::parse($date)->format('d M') }}
                                            </small>
                                        @endforeach
                                    @endif
                                </div>
                            @endfor
                            <div class="information ">
                                <p>Sudah Aktif {{ $paidPatent->count() }} dari 10 tahun  </p>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <div class="amount text-capitalize card px-3 text-center bg-gray-200 text-black">
                                <p class="mb-0 fw-500">Jumlah pembayaran</p>
                                <p class="mt-0">Rp{{ number_format($patent->amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="payment-btn">
                                <button class="btn btn-md btn-primary" data-bs-target="#inputPaymentInfo" data-bs-toggle="modal">Bayar</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">Informasi Sertikat Paten</div>
                    <div class="card-body">
                        <div>
                            <h6>Nomor Serifikat Paten</h6>
                            <p>{{ $patent->certification_number }}sdfw</p>
                        </div>
                        <div>
                            <h6>Download Sertifikat Paten</h6>
                            <a href="#" class="btn btn-sm btn-primary"
                                target="_blank"><i class="bi bi-file-arrow-down"></i>Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Status -->
    <div class="modal fade" id="inputPaymentInfo" tabindex="-1" aria-labelledby="inputPaymentInfoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Status Paten</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('patent.uploadPayment') }}" id="editStatusForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="patent_id" id="patent_id" value="{{ $patent->id }}">
                        <div class="mb-3">
                            <label for="payment_date">Tanggal Pembayaran</label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" placeholder="Masukkan Tanggal Pembayaran" required>
                        </div>
                        <div class="mb-3">
                            <label for="payment_proof" class="form-label">Bukti Pembayaran<label>
                            <input type="file" class="form-control" id="payment_proof" name="payment_proof" placeholder="Masukkan Registration Number" required>
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

<script>
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