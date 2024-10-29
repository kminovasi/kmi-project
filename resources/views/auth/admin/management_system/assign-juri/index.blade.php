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

    <!-- Form Pencarian dan Filter -->
    <form action="{{ route('management-system.juri') }}" method="GET" class="mb-4 p-0 ">

        <div class="flex row">
            <!-- Pencarian berdasarkan nama user -->
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari judul paper..."
                    value="{{ request('search') }}">
            </div>

            <!-- Filter berdasarkan company  -->
            <div class="col-md-3">
                <select name="company" class="form-select form-select-sm">
                    <option value="">-- Pilih Perusahaan --</option>
                    @foreach ( $companies as $company )
                    <option value="{{ $company->company_code }}">{{ $company->company_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter berdasarkan Event -->
            <div class="col-md-3">
                <select name="event" class="form-select form-select-sm">
                    <option value="">-- Pilih Event --</option>
                    @foreach ($events as $event)
                    <option value="{{ $event->id }}" {{ request('event')==$event->id ? 'selected' : '' }}>
                        {{ $event->event_name }} - {{ $event->year }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Submit -->
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm">Cari</button>
            </div>
        </div>
    </form>

    <div class="card px-2 pt-2">
        <div class="table-responsive">
            <table class="table table-borderless table-hover text-sm">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Perusahaan</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    @if ($judges->count() > 0)
                        @foreach ($judges as $j)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $j->name }}</td>
                            <td>{{ $j->company_name }}</td>
                            <td>{{ $j->event->event_name }} - {{ $j->event->year }}</td>
                            <td>
                                @if ($j->status == 'active')
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary">test</button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data</td>
                        </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>


    @if ($judges->hasPages())
    <div class="d-flex justify-content-end mt-2">
        <ul class="pagination">

            <li class="page-item {{ $judges->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $judges->previousPageUrl() }}" rel="prev"
                    aria-label="@lang('pagination.previous')">&lsaquo;</a>
            </li>


            @foreach ($judges->links()->elements[0] as $page => $url)
            <li class="page-item {{ $page == $judges->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
            </li>
            @endforeach


            <li class="page-item {{ $innovations->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $innovations->nextPageUrl() }}" rel="next"
                    aria-label="@lang('pagination.next')">&rsaquo;</a>
            </li>
        </ul>
    </div>
    @endif


</div>

@endsection
@push('js')
<script>
    function goBack() {
        window.history.back();
    }
</script>
@endpush
