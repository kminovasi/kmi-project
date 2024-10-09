@extends('layouts.app')
@section('title', 'Data Pemenang')


@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Dokumentasi - Evidence
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

    <!-- Main page content -->
    <div class="container-xl px-4 mt-4">

        <select class="form-select form-select-md mb-4" aria-label="Small select example">
            <option selected>Select Event Name</option>
            <option value="1">One</option>
            <option value="2">Two</option>
            <option value="3">Three</option>
        </select>

        <div class="row">
            <div class="col-lg-12">
                <div class="card px-4">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Team</th>
                                <th scope="col">Judul Paper</th>
                                <th scope="col">Juara</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <tr>
                                <th scope="row">1</th>
                                <td>Test Team</td>
                                <td>Tata Cara Masuk Islam</td>
                                <td>1</td>
                                <td>
                                    <a href="{{ route('evidence.detail') }}">Lihat Detail</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="pagination">
                        {{-- {{ $winners->links() }}  <!-- Ini akan menampilkan navigasi pagination --> --}}
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection
