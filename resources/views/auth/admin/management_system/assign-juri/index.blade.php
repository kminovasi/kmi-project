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
                            Management System - Juri
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('management-system.role.index') }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                        <a class="btn btn-sm btn-outline-success" href="{{ route('management-system.juri-export') }}">
                            <i class="me-1" data-feather="table"></i>
                            Excel
                        </a>
                        <a href="{{ route('management-system.juri-create') }}" class="btn btn-sm btn-primary ms-auto">
                            <i class="me-1" data-feather="plus"></i>
                            Tambah Juri
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-xl px-4 mt-4">

        {{-- Menampilkan pesan sukses --}}
        <x-toast-alert type="success" message="{{ session('success') }}" />
        {{-- Menampilkan pesan error --}}
        <x-toast-alert type="danger" message="{{ $errors->first('error') }}" />

        @livewire('judge-table')

    </div>

@endsection

