@extends('layouts.app')
@section('title', 'Data Paper')
@push('css')
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css"> --}}
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
<style type="text/css">
    .step-one h1 {
        text-align: center;
    }
    .step-one img{
        width: 75%;
        height: 75%;
    }
    .step-one p{
        text-align: justify;
    }
    .file-review{
        margin:20px 10px;
    }
    .active-link {
        color: #ffc004;
        background-color: #e81500;
    }
</style>
@endpush
@section('content')
                    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
                        <div class="container-xl px-4">
                            <div class="page-header-content">
                                <div class="row align-items-center justify-content-between pt-3">
                                    <div class="col-auto mb-3">
                                        <h1 class="page-header-title">
                                            <div class="page-header-icon"><i data-feather="file-text"></i></div>
                                             HASIL PENILAIAN ON DESK ASSESSMENT - {{ strtoupper($data['dataTeam']->event_name . ' Tahun ' . $data['dataTeam']->year) }}
                                        </h1>
                                    </div>
                                    <div class="col-12 col-xl-auto mb-3">
                                        <a class="btn btn-sm btn-light text-primary" href="{{route('assessment.on_desk')}}">
                                            <i class="me-1" data-feather="arrow-left"></i>
                                            Kembali
                                        </a>
                                        <a target="_blank" class="btn btn-sm btn-primary text-white" href="{{route('assessment.download.sofi.oda', $data['dataTeam']->event_team_id)}}">
                                            <i class="me-1" data-feather="download"></i>
                                            Download SOFI
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                    <!-- Main page content-->
                    <div class="container-xl px-4 mt-4">
                        <div class="card mb-4">
                            <div class="card-header">INFORMASI TIM</div>
                            <div class="card-body" id="datatable-card">
                                <div class="ms-4">
                                    <h6 class="mb-1">Nama Tim</h6>
                                    <p>{{$data['dataTeam']->team_name}}</p>
                                </div>
                                <hr>
                                <div class="ms-4">
                                    <h6 class="mb-1">Judul Inovasi</h6>
                                    <p>{{$data['dataTeam']->innovation_title}}</p>
                                </div>
                                <hr>
                                <div class="ms-4">
                                    <h6 class="mb-1">Lokasi Implementasi Inovasi</h6>
                                    <p>{{$data['dataTeam']->inovasi_lokasi}}</p>
                                </div>
                            </div>
                            
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">STRENGTH POINT OPPORTUNITY FOR IMPROVEMENT (SOFI)</div>
                            <div class="card-body">
                                <div class="ms-4">
                                    <h6 class="mb-1">SOFI : 1. Strength Point</h6>
                                    <p>{!! nl2br($data['dataTeam']->strength) !!}</p>
                                </div>
                                <hr>
                                <div class="ms-4">
                                    <h6 class="mb-1">SOFI : 2. Opportunity For Improvement</h6>
                                    <p>{!! nl2br($data['dataTeam']->opportunity_for_improvement) !!}</p>
                                </div>
                                <hr>
                                <div class="ms-4">
                                    <h6 class="mb-1">Real Benefit (Rp)</h6>
                                    <p>Rp. {{ number_format($data['dataTeam']->financial, 2, ',', '.') }}</p>
                                </div>
                                <hr>
                                <div class="ms-4">
                                    <h6 class="mb-1">Potensi Benefit (Rp)</h6>
                                    <p>Rp. {{ number_format($data['dataTeam']->potential_benefit, 2, ',', '.') }}</p>
                                </div>
                                <hr>
                                <div class="ms-4">
                                    <h6 class="mb-1">Potensi Replikasi</h6>
                                    @if($data['dataTeam']->potensi_replikasi == 'Bisa Direplikasi')
                                        <p>Bisa Direplikasi</p>
                                    @elseif($data['dataTeam']->potensi_replikasi == 'Tidak Bisa Direplikasi')
                                        <p>Tidak Bisa Direplikasi</p>
                                    @else
                                        <p>Nilai tidak valid</p>
                                    @endif
                                </div>
                                
                                <hr>
                                <div class="ms-4">
                                    <h6 class="mb-1">Detil Penghitungan Benefit (Real & Potensial)</h6>
                                    <p>{{$data['dataTeam']->suggestion_for_benefit}}</p>
                                </div>
                                <hr>
                                <div class="ms-4">
                                    <h6 class="mb-1">Rekomendasi Juri Terhadap Kategori Makalah Inovasi</h6>
                                    <p>{{$data['dataTeam']->recommend_category ?? 'Tetap'}}</p>
                                </div>
                            </div>
                        </div>
                        
                    </div>


@endsection

@push('js')
{{-- <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script> --}}
@endpush
