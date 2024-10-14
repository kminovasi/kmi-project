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
            {{-- @foreach ($events as $event)
                <option value="{{ $event->id }}">{{ $event->event_name }} - {{ $event->year }}</option>
            @endforeach --}}
        </select>

        <div class="row">
            <div class="card px-4 py-4">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                {{-- <th scope="col">No</th> --}}
                                <th scope="col">Judul</th>
                                <th scope="col">Tema</th>
                                <th scope="col">Event</th>
                                <th scope="col">Potensi Replikasi</th>
                                <th scope="col">Financial</th>
                                <th scope="col">Team</th>
                                <th scope="col">Final Score</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            @if ($papers->count() > 0)
                                @foreach ($papers as $paper)
                            <tr>
                                {{-- <th scope="row">1</th> --}}
                                <td>{{ $paper->innovation_title }}</td>
                                <td>{{ $paper->theme_name }}</td>
                                <td>{{ $paper->event_name }} {{ $paper->year }}</td>
                                <td>{{ $paper->potensi_replikasi }}</td>
                                <td>Rp.{{ number_format($paper->financial, 0, ',', '.') }}</td>
                                <td>{{ $paper->team_name }}</td>
                                <td>{{ $paper->final_score }}</td>
                                <td>
                                    <a href="{{ route('evidence.detail', $paper->team_id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    <a href="{{ asset('storage/' . $paper->full_paper) }}" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="5">Data tidak ditemukan</td>
                                </tr>
                            @endif

                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    {{-- {{ $winners->links() }}  <!-- Ini akan menampilkan navigasi pagination --> --}}
                </div>
            </div>

        </div>
    </div>
@endsection
