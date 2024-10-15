@extends('layouts.app')
@section('title', 'Detail Teams')


@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Dokumentasi - Detail Team
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

    <div class="container-xl md-px-4 mt-4">
        @foreach ($papers as $paper)

        <div class="card mb-4">
            <div class="card-header bg-danger">
                <h5 class="card-header-title text-white">Paper</h5>
            </div>
            <div class="card-body">
                <figure class="text-center">
                    <img class="img-fluid rounded" src="{{ asset('storage/'.$paper->innovation_photo) }}" alt="paper">
                </figure>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Judul</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->innovation_title }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Tema</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->theme_name }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Abstrak</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->abstract }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Status Inovasi</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->status_inovasi }}</p>
                    </div>
                </div>
                <hr>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Potensi Replikasi</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->potensi_replikasi }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Permasalahan</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->problem }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Permasalahan Utama</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->main_cause }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Solusi</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->solution }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ asset('storage/' . str_replace('f: ', '', $paper->full_paper)) }}" class="btn btn-primary" download="{{ $paper->innovation_title }}.pdf">
                    <i class="fas fa-download"></i> Download Paper
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-danger">
                <h5 class="card-header-title text-white">Penilaian</h5>
            </div>
            <div class="card-body">
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>On Desk</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->total_score_on_desk }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Presentation</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->total_score_presentation }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Caucus</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->total_score_caucus }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Final Score</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->final_score }}</p>
                    </div>
                </div>
                <div class="row mb-1">
                    <div class="col-md-3">
                        <p><strong>Best Of The Best</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->is_best_of_the_best ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="card mb-4">
            <div class="card-header bg-danger">
                <h5 class="card-header-title text-white">Benefit</h5>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3">
                        <p><strong>Financial</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>Rp {{ number_format($paper->financial, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <p><strong>Potential Benefit</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>Rp {{ number_format($paper->potential_benefit, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <p><strong>Non-Financial Impact</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->non_financial }}</p>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <p><strong>Final Score</strong>:</p>
                    </div>
                    <div class="col-md-9">
                        <p>{{ $paper->final_score }}</p>
                    </div>
                </div>
            </div>

        </div>

        @endforeach

        <div class="card mb-4">
            <div class="card-header bg-danger">
                <h5 class="card-header-title text-white">Anggota</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Posisi</th>
                                <th scope="col">Email</th>
                                <th scope="col">Perusahaan</th>
                                <th scope="col">Kode</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($teamMember as $member )
                            <tr>
                                <td>{{ $member->user->name }}</td>
                                <td>{{ $member->status }}</td>
                                <td>{{ $member->user->email }}</td>
                                <td>{{ $member->user->company_name }}</td>
                                <td>{{ $member->user->company_code }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


@endsection
