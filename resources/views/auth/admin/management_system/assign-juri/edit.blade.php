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
                        Management System - Edit Juri
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

    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if (session('success'))
        <div class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-bg-success">
                <strong class="me-auto">Success</strong>
                <small>Just now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('success') }}
            </div>
        </div>
        @endif

        @if ($errors->has('error'))
        <div class="toast text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header text-bg-danger">
                <strong class="me-auto">Error</strong>
                <small>Just now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                {{ session('error') }}
            </div>
        </div>
        @endif
    </div>

    <div class="card p-4">

        <div class="row ">
            <form action="{{ route('management-system.juri-update', $judges->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="col-6 mb-3">
                    <label class="form-label text-sm" for="userSearch">Pilih Juri</label>
                    <input class="form-control form-control-sm" type="text" value="{{ $name }}" aria-label="{{ $name }}" readonly>
                </div>

                <div class="col-6 mb-3">
                    <label for="eventSelect" class="form-label text-sm"> Event</label>
                    @livewire('event-select')
                </div>

                <div class="col-6 mb-3">
                    <label for="document" class="form-label text-sm">Dokumen Pendukung</label>
                    <input class="form-control form-control-sm" name="document" id="document" type="file"  accept="application/pdf">
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

    document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 3000
                });
            });
            toastList.forEach(toast => toast.show());
        });
</script>

@endpush
