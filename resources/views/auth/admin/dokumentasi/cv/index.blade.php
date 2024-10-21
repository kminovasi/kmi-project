@extends('layouts.app')
@section('title', 'Kategori Event')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
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

    .display {
        border-collapse: separate;
        /* Menggunakan border-collapse: separate untuk memberikan jarak antar border */
        border-spacing: 0;
        /* Jarak antar sel tabel */
        width: 100%;
        /* Mengatur lebar tabel */
    }

    .display thead th,
    .display tbody td {
        border: 1px solid #e0e0e0;
        /* Garis border yang lebih ringan */
        padding: 12px;
        /* Jarak dalam sel tabel */
        border-radius: 8px;
        /* Sudut border yang membulat */
    }

    .display thead th {
        background-color: #f9f9f9;
        /* Warna latar belakang header tabel */
        font-weight: bold;
        /* Menebalkan font pada header tabel */
    }

    .card {
        border-radius: 10px;
        /* Sudut border yang membulat pada kartu */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Bayangan halus pada kartu */
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5em 1em;
        /* Jarak antara tombol pagination */
        margin: 0 0.2em;
        /* Jarak antara tombol pagination */
        border-radius: 4px;
        /* Sudut border yang membulat pada tombol pagination */
        background-color: #f0f0f0;
        /* Warna latar belakang tombol pagination */
        border: 1px solid #ddd;
        /* Garis border pada tombol pagination */
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background-color: #e0e0e0;
        /* Warna latar belakang tombol saat hover */
    }

    .dataTables_wrapper .dataTables_info {
        padding: 0.5em;
        /* Jarak padding untuk informasi tabel */
    }

    .dataTables_wrapper .dataTables_filter input {
        margin-left: 0.5em;
        /* Jarak antara label filter dan input */
        border-radius: 4px;
        /* Sudut border yang membulat pada input filter */
        border: 1px solid #ddd;
        /* Garis border pada input filter */
        padding: 0.5em;
        /* Jarak di dalam input filter */
    }

    .dataTables_wrapper .dataTables_length select {
        margin-left: 0.5em;
        /* Jarak antara label length dan dropdown */
        border-radius: 4px;
        /* Sudut border yang membulat pada dropdown */
        border: 1px solid #ddd;
        /* Garis border pada dropdown */
        padding: 0.5em;
        /* Jarak di dalam dropdown */
    }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dt-buttons {
        display: flex;
        align-items: center;
    }

    .dataTables_wrapper .dataTables_length select {
        margin-left: 0.5em;
        /* Jarak antara label length dan dropdown */
        border-radius: 4px;
        /* Sudut border yang membulat pada dropdown */
        border: 1px solid #ddd;
        /* Garis border pada dropdown */
        padding: 0.5em;
        /* Jarak di dalam dropdown */
        font-size: 0.875em;
        /* Ukuran font dropdown */
    }

    .dataTables_wrapper .dt-buttons {
        margin-left: auto;
        /* Menggeser tombol ekspor ke kanan */
        display: flex;
        gap: 0.5em;
        /* Jarak antara tombol ekspor */
    }

    .dt-buttons .btn {
        background-color: #ffffff;
        /* Warna putih cerah */
        color: #000;
        /* Warna teks tombol, hitam untuk kontras */
        border: 2px solid #d6d8db;
        /* Garis border yang lebih tebal */
        border-radius: 4px;
        /* Sudut border yang membulat pada tombol */
        padding: 0.6em 1.2em;
        /* Jarak dalam tombol, agak diperbesar */
        font-size: 0.875em;
        /* Ukuran font tombol */
        text-transform: uppercase;
        /* Mengubah teks menjadi huruf kapital */
        cursor: pointer;
        /* Mengubah kursor saat hover pada tombol */
        display: inline-block;
        /* Agar tombol berperilaku sebagai elemen inline-block */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        /* Efek timbul pada tombol */
        transition: transform 0.2s ease-in-out, background-color 0.2s ease-in-out;
    }

    .dt-buttons .btn:hover {
        background-color: #f0f0f0;
        /* Warna abu-abu sangat cerah saat hover */
        transform: translateY(-2px);
        /* Sedikit mengangkat tombol saat hover */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
        /* Efek timbul lebih kuat saat hover */
        border-color: #c0c0c0;
        /* Warna border sedikit lebih gelap saat hover */
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
                        Dokumentasi - CV
                    </h1>
                </div>
                <div class="col-12 col-xl-auto mb-3">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('dokumentasi.index') }}">
                        <i class="me-1" data-feather="arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container-xl px-4 mt-4">
    <div class="card text-sm">
        <div class="card-body">
            <div class="table-responsive-lg">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Team</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Tema</th>
                            <th scope="col">Event</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Potensi Replikasi</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($innovations as $inovasi )
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $inovasi->team_name }}</td>
                            <td>{{ $inovasi->innovation_title }}</td>
                            <td>{{ $inovasi->theme_name }}</td>
                            <td>{{ $inovasi->event_name }} {{ $inovasi->year }}</td>
                            <td>{{ $inovasi->category }}</td>
                            <td>{{ $inovasi->potensi_replikasi }}</td>
                            <td>
                                @if ($inovasi->is_best_of_the_best == false)
                                {{ $inovasi->status }}
                                @else
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                    data-bs-title="Best of The Best">
                                    <i class="fas fa-trophy" aria-hidden="true"></i>
                                </button>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('cv.detail', $inovasi->team_id) }}"
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                                <a href="{{ asset('storage/' . str_replace('f: ', '', $inovasi->full_paper)) }}"
                                    class="btn btn-sm btn-warning" download="{{ $inovasi->innovation_title }}.pdf">
                                    <i data-feather="download"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
