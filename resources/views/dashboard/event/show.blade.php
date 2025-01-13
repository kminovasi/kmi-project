@extends('layouts.app')

@section('title', 'Detail Event')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">


@section('content')
<div class="container mt-4">
    <div class="card shadow-sm fade-in">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">DETAIL EVENT : {{ $event->event_name }}</h5>

        </div>
        <div class="card-body">
            <div class="mb-4">
                <h6 class="text-muted">Informasi Event</h6>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <strong>Nama Event:</strong>
                            <p class="mb-0">{{ $event->event_name }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <p class="mb-0">
                                @if ($event->status === 'active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Tidak Aktif</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <strong>Tanggal Mulai:</strong>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($event->date_start)->format('d M Y') }}</p>
                        </div>
                        <div class="mb-3">
                            <strong>Tanggal Berakhir:</strong>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($event->date_end)->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <strong>Deskripsi:</strong>
                            <p class="mb-0">{{ $event->description }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <a href="{{ route('dashboard-event.list') }}" class="btn btn-primary btn-sm">
                Kembali ke Event
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        /* Animasi Fade-In */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Animasi untuk hover tombol kembali */
        .btn-outline-primary {
            transition: transform 0.2s ease, box-shadow 0.3s ease;
        }

        .btn-outline-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }
    </style>
@endpush

