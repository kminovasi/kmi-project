@extends('layouts.app')
@section('title', 'Kategori Event')
@section('content')

<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-2">
    <div class="container-xl px-4">
        <div class="page-header-content">
            <div class="row align-items-center justify-content-between pt-3">
                <div class="col-auto mb-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i data-feather="book"></i></div>
                        Management System - Tambah Juri
                    </h1>
                </div>
                <div class="col-12 col-xl-auto mb-3">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('management-system.juri') }}">
                        <i class="me-1" data-feather="arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container-xl px-4 mt-4">

    <div class="mb-3">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if ($errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            {{ $errors->first('error') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>

    <div class="card p-4">

        <div class="row ">
            <form action="{{ route('management-system.juri-store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="col-6 mb-3">
                    <label class="form-label text-sm" for="userSearch">Pilih Juri</label>
                    @livewire('user-select')
                </div>

                <div class="col-6 mb-3">
                    <label for="eventSelect" class="form-label text-sm"> Event</label>
                    @livewire('event-select')
                </div>

                <div class="col-6 mb-3">
                    <label for="document" class="form-label text-sm">Dokumen Pendukung</label>
                    <input class="form-control form-control-sm" name="document" id="document" type="file" accept="application/pdf" required>
                </div>

                <div class="col-6 mb-3">
                    <label for="switch" class="form-label text-sm">Status</label>
                    @livewire('switches')
                </div>

                <button type="submit" class="btn btn-sm btn-primary">Submit</button>
            </form>
        </div>

    </div>

</div>

@endsection
@push('js')

<script>
    function goBack() {
        window.history.back();
    }
</script>

@endpush
