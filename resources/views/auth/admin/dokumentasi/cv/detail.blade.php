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
                        Evidence - Detail Team
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

<div class="container-xl md-px-4 mt-4">
    @foreach ($papers as $paper)

    <div class="card mb-4">
        <div class="card-header bg-danger">
            <h5 class="card-header-title text-white">Paper</h5>
        </div>
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-5 mb-3 text-center border rounded">
                    <img src="{{ route('query.getFile') }}?directory={{ urlencode($paper->proof_idea) }}"
                         id="fotoTim" class="img-fluid rounded"
                         style="max-width: 30rem;">
                    <figcaption class="figure-caption text-center">Foto Tim</figcaption>
                </div>
                <div class="col-md-5 mb-3 text-center border rounded">
                    <img src="{{ route('query.getFile') }}?directory={{ urlencode($paper->innovation_photo) }}"
                         id="fotoTim" class="img-fluid rounded"
                         style="max-width: 30rem;">
                    <figcaption class="figure-caption text-center">Foto Inovasi</figcaption>
                </div>
            </div>

            <hr>
            <div class="row mb-1">
                <div class="col-md-3">
                    <p><strong>Nama Tim</strong>:</p>
                </div>
                <div class="col-md-9">
                    <p>{{ $paper->team_name }}</p>
                </div>
            </div>
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
                    <p>{!! nl2br(e($paper->abstract)) !!}</p>
                </div>
            </div>
            <hr>
            <div class="row mb-1">
                <div class="col-md-3">
                    <p><strong>Status Inovasi</strong>:</p>
                </div>
                <div class="col-md-9">
                    <p>{{ $paper->status_inovasi }}</p>
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-md-3">
                    <p><strong>Potensi Replikasi</strong>:</p>
                </div>
                <div class="col-md-9">
                    <p>{{ $paper->potensi_replikasi }}</p>
                </div>
            </div>
            <hr>
            <div class="row mb-1">
                <div class="col-md-3">
                    <p><strong>Permasalahan</strong>:</p>
                </div>
                <div class="col-md-9">
                    <p>{!! nl2br(e($paper->problem)) !!}</p>
                </div>
            </div>
            <hr>
            <div class="row mb-1">
                <div class="col-md-3">
                    <p><strong>Permasalahan Utama</strong>:</p>
                </div>
                <div class="col-md-9">
                    <p>{!! nl2br(e($paper->main_cause)) !!}</p>
                </div>
            </div>
            <hr>
            <div class="row mb-1">
                <div class="col-md-3">
                    <p><strong>Solusi</strong>:</p>
                </div>
                <div class="col-md-9">
                    <p>{!! nl2br(e($paper->solution)) !!}</p>
                </div>
            </div>
        </div>
        <div class="card-footer  d-flex justify-content-end">
            <a href="{{ route('paper.watermarks', ['paper_id' => $paper->id]) }}" class="btn btn-primary me-2" target="_blank"
                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip" data-bs-title="Download Paper">
                <i class="fas fa-download me-1"></i> Download Makalah
            </a>
            @if (!empty($paper->path))
            <a href="{{ asset('storage/' . $paper->path) }}" class="btn btn-secondary" download="Supporting Document"
                data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                data-bs-title="Download Dokumen Pendukung">
                <i class="fas fa-download me-1"></i> Download Dokumen Pendukung
            </a>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-danger">
            <h5 class="card-header-title text-white">Penilaian</h5>
        </div>
        <div class="card-body">
            <div class="row mb-1">
                <div class="col-md-3">
                    <p><strong> Status Tim</strong>:</p>
                </div>
                <div class="col-md-9">
                    <p>{{ $paper->team_status }}</p>
                </div>
            </div>
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
                    <p><strong>Potensi Replikasi</strong>:</p>
                </div>
                <div class="col-md-9">
                    <p>{{ $paper->potensi_replikasi }}</p>
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
                            <th scope="col">NIK</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Status</th>
                            <th scope="col">Email</th>
                            <th scope="col">Perusahaan</th>
                            <th scope="col">Kode</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($teamMember as $member )
                        <tr>
                            <td>{{ $member->user->employee_id }}</td>
                            <td>{{ $member->user->name }}</td>
                            @if($member->status == 'gm')
                                <td>Penanggung Jawab Benefit</td>
                            @elseif($member->status == 'leader')
                                <td>Ketua Tim</td>
                            @elseif($member->status == 'member')
                                <td>Anggota</td>
                            @elseif($member->status == 'facilitator')
                                <td>Fasilitator</td>
                            @endif
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
    
    <!-- Anggota Tim Outsource -->
    @if(!empty($outsourceMember))
    <div class="card mb-4">
        <div class="card-header bg-danger">
            <h5 class="card-header-title text-white">Anggota Tim Outsource</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Nama</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($outsourceMember as $member)
                            <tr>
                                <td>{{ $member->name }}</td>
                                <td>Outsource</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
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
