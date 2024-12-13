@extends('layouts.app')

@section('title', 'Detail Event')

@section('content')
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>Detail Event: {{ $event->event_name }}</h4>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th>Nama Event:</th>
                        <td>{{ $event->event_name }}</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            @if ($event->status === 'active' && now()->between($event->date_start, $event->date_end))
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Mulai:</th>
                        <td>{{ \Carbon\Carbon::parse($event->date_start)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Berakhir:</th>
                        <td>{{ \Carbon\Carbon::parse($event->date_end)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <th>Deskripsi:</th>
                        <td>{{ $event->description }}</td>
                    </tr>
                </table>
                <a href="{{ route('dashboard-event.list') }}" class="btn btn-secondary mt-3">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Event
                </a>
            </div>
        </div>
    </div>
@endsection
