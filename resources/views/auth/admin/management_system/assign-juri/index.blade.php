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

        <!-- Form Pencarian dan Filter -->
        @if (Auth::user()->role == 'Superadmin')
            <form action="{{ route('management-system.juri') }}" method="GET" class="mb-4 p-0 ">
                <div class="flex row">
                    <!-- Pencarian berdasarkan nama user -->
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Cari nama juri..." value="{{ request('search') }}">
                    </div>

                    <!-- Filter berdasarkan company  -->
                    <div class="col-md-3">
                        @livewire('company-select')
                    </div>

                    <!-- Filter berdasarkan Event -->
                    <div class="col-md-3">
                        @livewire('event-select')
                    </div>

                    <!-- Tombol Submit -->
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    </div>
                </div>
            </form>
        @endif


        <div class="toast-container position-fixed top-0 end-0 p-3">
            @if (session('success'))
                <div class="toast text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header text-bg-success">
                        <strong class="me-auto">Success</strong>
                        <small>Just now</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                            aria-label="Close"></button>
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
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                            aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

        <div class="table-responsive min-vh-100">
            <table class="table table-borderless table-hover text-sm rounded bg-white">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Perusahaan</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                    @if ($judges->count() > 0)
                        @foreach ($judges as $index => $j)
                            <tr>
                                <td>{{ ($currentPage - 1) * $perPage + $index + 1 }}</td>
                                <td>{{ $j->name }}</td>
                                <td>{{ $j->company_name }}</td>
                                <td>{{ $j->event->event_name }} {{ $j->event->year }}</td>
                                <td>
                                    @if ($j->status == 'active')
                                        <span class="badge bg-success">{{ $j->status }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ $j->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i data-feather="more-horizontal"></i>
                                        </button>

                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" type="button"
                                                    href="{{ route('management-system.juri-edit', [$j->id, $j->name]) }}">Edit</a>
                                            </li>
                                            <li>
                                                <form action="{{ route('management-system.juri-updateStatus', $j->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <button class="dropdown-item" type="submit">Update Status</button>
                                                </form>
                                            </li>
                                            <li><a class="dropdown-item"
                                                    href="{{ asset('storage/surat-juri/' . $j->letter_path) }}"
                                                    target="_blank">Lihat Surat</a></li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $loop->iteration }}">Hapus</a>
                                            </li>
                                        </ul>

                                    </div>

                                    {{-- Modal Delete --}}
                                    <div class="modal fade" id="deleteModal{{ $loop->iteration }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content ">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="deleteModalLabel">Konfirmasi Hapus
                                                        Data
                                                    </h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Apakah yakin data ini akan dihapus ?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-primary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <form action="{{ route('management-system.juri-delete', $j->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Ya,
                                                            Hapus</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

                        <li class="page-item {{ $judges->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $judges->nextPageUrl() }}" rel="next"
                                aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

    </div>

@endsection
@push('js')
    <script>
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
