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
                            Evidence - Daftar Inovasi
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <!-- Tombol Print -->
                        <a class="btn btn-sm btn-outline-success" href="{{ route('evidence.excel', $category->id) }}">
                            <i class="me-1" data-feather="table"></i>
                            Excel
                        </a>

                        <a class="btn btn-sm btn-outline-primary" href="{{ route('evidence.index') }}">
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

        <!-- Form Pencarian dan Filter -->
        <form action="{{ route('evidence.category', $category->id) }}" method="GET" class="mb-4 p-0 ">

            <div class="flex row">
                <!-- Pencarian berdasarkan judul paper -->
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari judul paper..." value="{{ request('search') }}">
                </div>

                <!-- Filter berdasarkan company code  -->
                <div class="col-md-2">
                    @livewire('company-select')
                </div>

                <!-- Filter berdasarkan Event -->
                <div class="col-md-2">
                    @livewire('event-select')
                </div>

                <!-- Filter berdasarkan tema -->
                <div class="col-md-2">
                    @livewire('theme-select')
                </div>

                <!-- Tombol Submit -->
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                </div>
            </div>
        </form>

        <div class="table-responsive min-vh-100">
            {{-- table --}}
            <table class="table table-borderless table-hover text-sm rounded bg-white">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Team</th>
                        <th scope="col">Judul</th>
                        <th scope="col">Tema</th>
                        <th scope="col">Event</th>
                        <th scope="col">Financial</th>
                        <th scope="col">Potensi Replikasi</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @if ($papers->count() > 0)
                        @foreach ($papers as $paper)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $paper->team_name }}</td>
                                <td>{{ $paper->innovation_title }}</td>
                                <td>{{ $paper->theme_name }}</td>
                                <td>{{ $paper->event_name }} {{ $paper->year }}</td>
                                <td>Rp.{{ number_format($paper->financial, 0, ',', '.') }}</td>
                                <td>{{ $paper->potensi_replikasi }}</td>
                                <td>
                                    @if ($paper->is_best_of_the_best == false)
                                        {{ $paper->status }}
                                    @else
                                        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="tooltip"
                                            data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                            data-bs-title="Best of The Best">
                                            <i class="fas fa-trophy" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i data-feather="more-horizontal"></i>
                                        </button>

                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{ route('evidence.detail', $paper->team_id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-info-circle dropdown-item-icon"></i> Detail
                                                </a>
                                            </li>
                                            <hr class="dropdown-divider">
                                            <li>
                                                <a href="{{ asset('storage/' . str_replace('f: ', '', $paper->full_paper)) }}"
                                                    class="dropdown-item" download="{{ $paper->innovation_title }}.pdf">
                                                    <i class="fas fa-download dropdown-item-icon"></i>  Download Paper
                                                </a>
                                            </li>
                                        </ul>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="text-center" colspan="9">Data tidak ditemukan</td>
                        </tr>
                    @endif

                </tbody>
            </table>

            {{-- pagginate --}}
            @if ($papers->hasPages())
                <div class="d-flex justify-content-end mt-2">
                    <ul class="pagination">
                        {{-- Tombol Previous --}}
                        <li class="page-item {{ $papers->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $papers->previousPageUrl() }}" rel="prev"
                                aria-label="@lang('pagination.previous')">&lsaquo;</a>
                        </li>

                        {{-- Nomor Halaman --}}
                        @foreach ($innovations->links()->elements[0] as $page => $url)
                            <li class="page-item {{ $page == $innovations->currentPage() ? 'active' : '' }}">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endforeach

                        {{-- Tombol Next --}}
                        <li class="page-item {{ $innovations->hasMorePages() ? '' : 'disabled' }}">
                            <a class="page-link" href="{{ $innovations->nextPageUrl() }}" rel="next"
                                aria-label="@lang('pagination.next')">&rsaquo;</a>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

    </div>

@endsection
