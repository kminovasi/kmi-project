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
                    <a class="btn btn-sm btn-outline-primary" onclick="goBack()">
                        <i class="me-1" data-feather="arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container-xl px-4 mt-4">

    <div class="card">
        <div class="card-header">

        </div>
        <div class="card-body">

        </div>
        <div class="card-footer">

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
