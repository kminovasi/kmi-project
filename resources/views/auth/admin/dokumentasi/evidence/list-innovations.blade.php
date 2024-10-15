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

        <!-- Form Pencarian dan Filter -->
        <form action="{{ route('evidence.category', $category->id) }}" method="GET" class="mb-4 p-0 ">

            <div class="flex row">
                <!-- Pencarian berdasarkan judul paper -->
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari judul paper..." value="{{ request('search') }}">
                </div>

                <!-- Filter berdasarkan status  -->
                {{-- <div class="col-md-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">-- Pilih Status --</option>
                        <option value="Juara" {{ request('status') == 'Juara' ? 'selected' : '' }}>Juara</option>
                        <option value="Non-Juara" {{ request('status') == 'Non-Juara' ? 'selected' : '' }}>Non-Juara
                        </option>
                    </select>
                </div> --}}

                <!-- Filter berdasarkan Event -->
                <div class="col-md-3">
                    <select name="event" class="form-select form-select-sm">
                        <option value="">-- Pilih Event --</option>
                        @foreach ($events as $event)
                            <option value="{{ $event->id }}" {{ request('event') == $event->id ? 'selected' : '' }}>
                                {{ $event->event_name }} - {{ $event->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter berdasarkan tema -->
                <div class="col-md-3">
                    <select name="theme" class="form-select form-select-sm">
                        <option value="">-- Pilih theme --</option>
                        @foreach ($themes as $theme)
                            <option value="{{ $theme->id }}"
                                {{ request('theme') == $theme->theme_name ? 'selected' : '' }}>
                                {{ $theme->theme_name }}
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


        {{-- <select class="form-select form-select-md mb-4" aria-label="Small select example">
            @foreach ($events as $event)
                <option value="{{ $event->id }}">{{ $event->event_name }} - {{ $event->year }}</option>
            @endforeach
        </select> --}}

        <div class="row">
            <div class="card px-2 py-2">
                <div class="table-responsive">

                    <table class="table table-borderless table-hover text-sm">
                        <thead>
                            <tr>
                                <th scope="col">Judul</th>
                                <th scope="col">Tema</th>
                                <th scope="col">Event</th>
                                <th scope="col">Potensi Replikasi</th>
                                <th scope="col">Financial</th>
                                <th scope="col">Team</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @if ($papers->count() > 0)
                                @foreach ($papers as $paper)
                                    <tr>
                                        <td>{{ $paper->innovation_title }}</td>
                                        <td>{{ $paper->theme_name }}</td>
                                        <td>{{ $paper->event_name }} {{ $paper->year }}</td>
                                        <td>{{ $paper->potensi_replikasi }}</td>
                                        <td>Rp.{{ number_format($paper->financial, 0, ',', '.') }}</td>
                                        <td>{{ $paper->team_name }}</td>
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
                                            <a href="{{ route('evidence.detail', $paper->team_id) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fas fa-info-circle"></i>
                                            </a>
                                            <a href="{{ asset('storage/' . $paper->full_paper) }}"
                                                class="btn btn-sm btn-secondary">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="8">Data tidak ditemukan</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>

                </div>
                <div class="pagination">
                    {{ $papers->links() }}
                </div>
            </div>

        </div>
    </div>

@endsection
