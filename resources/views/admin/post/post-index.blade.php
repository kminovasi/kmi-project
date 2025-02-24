@extends('layouts.app')
@section('title', 'post')
@section('content')
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
@endpush


<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
    <div class="container-xl px-4">
        <div class="page-header-content">
            <div class="row align-items-center justify-content-between pt-3">
                <div class="col-auto mb-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i data-feather="image"></i></div>
                        Manajement Post
                    </h1>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container-xl px-4 mt-4">
    {{-- notif --}}
    {{-- Menampilkan pesan sukses --}}
    <x-toast-alert type="success" message="{{ session('success') }}" />
    {{-- Menampilkan pesan error --}}
    <x-toast-alert type="danger" message="{{ $errors->first('error') }}" />

    <div>
        <a class="btn btn-primary mb-3" href="{{ route('post.create') }}">Buat Postingan</a>
    </div>

    @livewire('post-table')

</div>

@endsection
