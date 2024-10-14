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

    <div class="container-xl px-4 mt-4">
        @foreach ($papers as $paper)

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-header-title">Paper</h5>
            </div>
            <div class="card-body">
                <p> <strong> Judul:</strong>  {{ $paper->innovation_title }}</p>
                <p><strong>Tema:</strong> {{ $paper->theme_name }}</p>
                <p><strong>Status Inovasi:</strong> {{ $paper->status_inovasi }}</p>
                <p><strong>Potensi Replikasi:</strong> {{ $paper->potensi_replikasi }}</p>
                <p><strong>Abstrak:</strong> {{ $paper->abstract }}</p>
                <p><strong>Permasalahan:</strong> {{ $paper->problem }}</p>
                <p><strong>Solusi:</strong> {{ $paper->solution }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-header-title">Penilaian</h5>
            </div>
            <div class="card-body">
                <p><strong>On Desk</strong> : {{ $paper->total_score_on_desk }}</p>
                <p><strong>Presentation</strong> : {{ $paper->total_score_presentation }}</p>
                <p><strong>Caucus</strong> : {{ $paper->total_score_caucus }}</p>
                <p><strong>Final Score</strong> : {{ $paper->final_score }}</p>
                <p><strong>Is Best Of The Best</strong> : {{ $paper->is_best_of_the_best }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header text-black">
                Benefit
            </div>
            <div class="card-body">
                <p><strong>Financial:</strong> Rp {{ number_format($paper->financial, 0, ',', '.') }}</p>
                <p><strong>Potential Benefit:</strong> Rp {{ number_format($paper->potential_benefit, 0, ',', '.') }}</p>
                <p><strong>Non-Financial Impact:</strong> {{ $paper->non_financial }}</p>
                <p><strong>Final Score:</strong> {{ $paper->final_score }}</p>
            </div>
        </div>

        @endforeach

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-header-title">Anggota</h5>
            </div>
            <div class="card-body">
                @foreach ($teamMember as $member )
                <li>{{ $member->user->name }} {{ $member->status }}</li> <!-- Menampilkan nama anggota tim -->
                @endforeach
            </div>
        </div>
    </div>


@endsection
