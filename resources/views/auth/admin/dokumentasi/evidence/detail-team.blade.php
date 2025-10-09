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
                            Evidence - Detail Team {{ $team->team_name }}
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-outline-primary" onclick="goBack()">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                        <button type="button"
                                class="btn btn-sm btn-warning ms-2"
                                onclick="confirmReplicationSwal('{{ route('replications.create', $teamId) }}')">
                                <i class="fas fa-share-square me-1"></i> Ajukan Replikasi
                        </button>
                        @if(Auth::user()->role == 'Superadmin')
                        <a href="{{ route('evidence.downloadWord', $teamId) }}" class="btn btn-sm btn-outline-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-word-fill me-1" viewBox="0 0 16 16">
                                <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M5.485 6.879l1.036 4.144.997-3.655a.5.5 0 0 1 .964 0l.997 3.655 1.036-4.144a.5.5 0 0 1 .97.242l-1.5 6a.5.5 0 0 1-.967.01L8 9.402l-1.018 3.73a.5.5 0 0 1-.967-.01l-1.5-6a.5.5 0 1 1 .97-.242z"/>
                            </svg>
                            Download Word
                        </a>
                        @endif
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
                    <hr>
                    <div class="row mb-1">
                        <div class="col-md-3">
                            <p><strong>Aksi Makalah</strong>:</p>
                        </div>
                        <div class="col-md-9">
                            @if(in_array(Auth::user()->role, ['Admin', 'Superadmin']))
                                {{-- Admin & Superadmin bisa download --}}
                                <a href="{{ route('evidence.download-paper', $paper->paper_id) }}"
                                    class="btn btn-sm btn-primary me-2"
                                    download="{{ $paper->innovation_title }}.pdf"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-custom-class="custom-tooltip"
                                    data-bs-title="Download Makalah">
                                    <i class="fas fa-download"></i> Download Paper
                                </a>
                            @else
                                {{-- Role lain hanya preview --}}
                                <a href="{{ route('evidence.preview-paper', $paper->paper_id) }}"
                                    class="btn btn-sm btn-secondary"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    data-bs-custom-class="custom-tooltip"
                                    data-bs-title="Preview Makalah">
                                    <i class="fas fa-eye"></i> Preview Paper
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-1">
                        <div class="col-md-3">
                            <p><strong>Dokumen pendukung</strong>:</p>
                        </div>
                        <div class="col-md-9">
                            @if (empty($paper->path) || $paper->path == null)
                            Tidak ada dokumen
                            @else
                            <a href="{{ asset('storage/' . $paper->path) }}" class="btn btn-sm btn-secondary"
                                download="Supporting Document" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip" data-bs-title="Download Dokumen Pendukung">
                                <i class="fas fa-download "></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($isMember)
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
            @endif

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

        <!-- Anggota Tim Organik -->
        <div class="card mb-4">
            <div class="card-header bg-danger">
                <h5 class="card-header-title text-white">Anggota Tim</h5>
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
                            @foreach ($teamMember as $member)
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

        <!-- Telah di Replikasi Oleh -->
<div class="card mb-4">
    <div class="card-header bg-danger">
        <h5 class="card-header-title text-white">Telah di Replikasi Oleh</h5>
    </div>
    <div class="card-body">
        @if(($replicatedBy ?? collect())->isEmpty())
            <div class="text-muted">Belum ada replikasi</div>
        @else
            <div class="table-responsive">
                <table class="table table-borderless table-hover align-middle">
                    <thead>
                        <tr>
                            <th>PIC Replikator</th>
                            <th>Kontak</th>
                            <th>Unit / Plant</th>
                            <th>Tgl Rencana</th>
                            <th>Benefit (Fin / Pot)</th>
                            <th>Berkas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($replicatedBy as $rep)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $rep->pic_name }}</div>
                                    {{-- <small class="badge bg-primary">Replicated</small> --}}
                                </td>
                                <td>
                                    <div>{{ $rep->pic_phone ?: '—' }}</div>
                                    <small class="text-muted">
                                        {{ optional($rep->creator)->email ?? '—' }}
                                    </small>
                                </td>
                                <td>
                                    <div>{{ $rep->unit_name ?: '—' }}</div>
                                    <small class="text-muted">{{ $rep->plant_name ?: '—' }}</small>
                                </td>
                                <td>
                                    {{ $rep->planned_date ? \Carbon\Carbon::parse($rep->planned_date)->format('d M Y') : '—' }}
                                </td>
                                <td>
                                    Rp {{ number_format($rep->financial_benefit ?? 0, 0, ',', '.') }}
                                    /
                                    Rp {{ number_format($rep->potential_benefit ?? 0, 0, ',', '.') }}
                                </td>
                                <td>
                                    @php
                                        $files = is_array($rep->files) ? $rep->files : [];
                                    @endphp
                                    @if(empty($files))
                                        <span class="text-muted">—</span>
                                    @else
                                        <ul class="list-unstyled mb-0">
                                            @foreach($files as $f)
                                                @php
                                                    $path = $f['path'] ?? null;
                                                    $name = $f['name'] ?? basename($path ?? '');
                                                    // gunakan Storage::url kalau pakai disk public
                                                    $url  = $path ? Storage::url($path) : null;
                                                @endphp
                                                <li class="mb-1">
                                                    @if($url)
                                                        <a href="{{ $url }}" target="_blank">{{ $name }}</a>
                                                    @else
                                                        <span class="text-muted">{{ $name }}</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

    </div>

@endsection
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function goBack() {
            window.history.back();
        }

        function confirmReplicationSwal(url) {
        Swal.fire({
            title: 'Ajukan Replikasi?',
            text: 'Apakah anda ingin mengajukan replikasi makalah ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, ajukan',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            buttonsStyling: true,
            confirmButtonColor: '#D84040',  // merah SIG
            cancelButtonColor: '#6c757d',   // secondary
            backdrop: true,
            allowOutsideClick: () => !Swal.isLoading(),
            showLoaderOnConfirm: true,
            preConfirm: async () => {
            // Bisa ditambah pre-check async di sini kalau perlu
            return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
            // Opsional: kasih feedback singkat sebelum pindah
            Swal.fire({
                title: 'Membuka Form Replikasi…',
                timer: 600,
                didOpen: () => Swal.showLoading()
            }).then(() => {
                window.location.href = url;
            });
            }
        });
        }
    </script>
@endpush

