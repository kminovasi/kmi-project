@extends('layouts.app')
@section('title', 'Daftar ' . ucfirst($tipe))

@section('content')

@php
    $employee    = Auth::user();
    $innovations = $items; 
@endphp

<div class="container-xl px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Prestasi — {{ ucfirst($tipe) }}</h4>
            <div class="text-muted">Karyawan: <strong>{{ $namaKaryawan }}</strong> ({{ $employeeId }})</div>
        </div>
        <a class="btn btn-light" onclick="window.history.back()">
            <i data-feather="arrow-left" class="me-1"></i>Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:60px;">No</th>
                        @if($tipe === 'inovasi')
                            <th>Team</th>
                            <th>Judul</th>
                            <th>Tema</th>
                            <th>Event</th>
                            <th>Kategori</th>
                            <th>Replikasi</th>
                            <th>Status</th>
                            <th style="width:160px">Action</th>
                        @elseif($tipe === 'paten')
                            <th>Judul Paten</th>
                            <th>PIC</th>
                            <th>Team</th>
                            <th>Event</th>
                            <th>Tahun</th>
                        @else {{-- replikasi --}}
                            <th>Judul</th>
                            <th>Team</th>
                            <th>Tema</th>
                            <th>Kategori</th>
                            <th>Event</th>
                            <th>Tahun</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if ($innovations->isEmpty())
                        <tr><td colspan="9" class="text-center text-muted">Tidak ada data</td></tr>
                    @else
                        @foreach ($innovations as $row)
                            @if($tipe === 'inovasi')
                                @php
                                    $inovasi = (object)[
                                        'id'                    => $row->id,
                                        'team_id'               => $row->team_id,
                                        'team_name'             => $row->team_name,
                                        'innovation_title'      => $row->innovation_title,
                                        'theme_name'            => $row->theme_name ?? null,
                                        'event_name'            => $row->event_name ?? null,
                                        'year'                  => $row->year ?? null,
                                        'category'              => $row->category_name ?? ($row->category ?? null),
                                        'potensi_replikasi'     => $row->potensi_replikasi ?? '-',
                                        'is_best_of_the_best'   => property_exists($row,'is_best_of_the_best') ? $row->is_best_of_the_best : 0,
                                        'is_honorable_winner'   => property_exists($row,'is_honorable_winner') ? $row->is_honorable_winner : 0,
                                        'event_id'              => $row->event_id ?? null,
                                        // field untuk sertifikat:
                                        'certificate'           => $row->certificate ?? null,
                                        'special_certificate'   => $row->special_certificate ?? null,
                                        'badge_rank_1'          => $row->badge_rank_1 ?? null,
                                        'badge_rank_2'          => $row->badge_rank_2 ?? null,
                                        'badge_rank_3'          => $row->badge_rank_3 ?? null,
                                        'event_end'             => $row->event_end ?? null,
                                        'company_name'          => $row->company_name ?? null,
                                    ];

                                    $authEmpId = $employee->employee_id ?? null;
                                    $isGMForThisTeam = false;
                                    if ($authEmpId) {
                                        $statuses = \DB::table('pvt_members')
                                            ->where('team_id', $inovasi->team_id)
                                            ->where('employee_id', $authEmpId)
                                            ->pluck('status');
                                        if ($statuses->isNotEmpty()) {
                                            $isGMForThisTeam = $statuses->every(function($s){
                                                return strtolower(trim((string)$s)) === 'gm';
                                            });
                                        }
                                    }

                                    $rankData   = $teamRanks[$inovasi->team_id] ?? null;
                                    $hideForEvt = in_array((int) ($inovasi->event_id ?? 0), [3,4], true);
                                @endphp

                                <tr>
                                    <td>{{ ($innovations->currentPage()-1)*$innovations->perPage() + $loop->iteration }}</td>
                                    <td>{{ $inovasi->team_name }}</td>
                                    <td>{{ $inovasi->innovation_title }}</td>
                                    <td>{{ $inovasi->theme_name ?? '-' }}</td>
                                    <td>{{ $inovasi->event_name }} Tahun {{ $inovasi->year }}</td>
                                    <td>{{ $inovasi->category ?? '-' }}</td>
                                    <td>{{ $inovasi->potensi_replikasi ?? '-' }}</td>

                                    {{-- STATUS --}}
                                    <td>
                                        @if ($inovasi->is_best_of_the_best)
                                            <span class="badge bg-warning text-dark" title="Best of The Best">
                                                <i class="fas fa-trophy me-1"></i> BotB
                                            </span>
                                        @elseif ($inovasi->is_honorable_winner)
                                            <span class="badge bg-info text-dark">Juara Harapan</span>
                                        @elseif ($rankData && ($rankData->score ?? 0) > 0 && ($rankData->rank ?? 99) <= 3)
                                            <span class="badge bg-success">Juara {{ $rankData->rank }}</span>
                                        @else
                                            <span class="badge bg-secondary">Peserta</span>
                                        @endif
                                    </td>

                                    {{-- ACTION --}}
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i data-feather="more-horizontal"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                {{-- Detail Inovasi --}}
                                                <li>
                                                    <a href="{{ route('cv.detail', $inovasi->team_id) }}"
                                                       class="dropdown-item">
                                                        <i class="fas fa-info-circle me-2"></i>Detail Inovasi
                                                    </a>
                                                </li>

                                                {{-- Sertifikat Peserta (individual) --}}
                                                @if(!$hideForEvt && !$isGMForThisTeam)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('cv.generateCertificate') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="inovasi" value='@json($inovasi)'>
                                                        <input type="hidden" name="employee" value='@json($employee)'>
                                                        <input type="hidden" name="team_rank" value='@json(optional($rankData)->rank)'>
                                                        <input type="hidden" name="certificate_type" value="participant">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-download me-2"></i>Sertifikat Peserta
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif

                                                {{-- Sertifikat Tim --}}
                                                @if(!$hideForEvt && ($rankData && (int)($rankData->rank ?? 99) <= 3) && !$inovasi->is_honorable_winner)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('cv.generateCertificate') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="inovasi" value='@json($inovasi)'>
                                                        <input type="hidden" name="employee" value='@json($employee)'>
                                                        <input type="hidden" name="team_rank" value='@json(optional($rankData)->rank)'>
                                                        <input type="hidden" name="certificate_type" value="team">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-download me-2"></i>Sertifikat Tim
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif

                                                {{-- Best of the Best / Honorable Winner --}}
                                                @if(!$hideForEvt && $inovasi->is_best_of_the_best)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('cv.generateCertificate') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="inovasi" value='@json($inovasi)'>
                                                        <input type="hidden" name="employee" value='@json($employee)'>
                                                        <input type="hidden" name="certificate_type" value="best_of_the_best">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-download me-2"></i>Sertifikat Best of The Best
                                                        </button>
                                                    </form>
                                                </li>
                                                @elseif(!$hideForEvt && $inovasi->is_honorable_winner)
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('cv.generateCertificate') }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="inovasi" value='@json($inovasi)'>
                                                        <input type="hidden" name="employee" value='@json($employee)'>
                                                        <input type="hidden" name="certificate_type" value="honorable_winner">
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-download me-2"></i>Sertifikat Juara Harapan
                                                        </button>
                                                    </form>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                            @elseif($tipe === 'paten')
                                <tr>
                                    <td>{{ ($innovations->currentPage()-1)*$innovations->perPage() + $loop->iteration }}</td>
                                    <td>{{ $row->patent_title }}</td>
                                    <td>{{ $row->pic_name ?? '-' }}</td>
                                    <td>{{ $row->team_name ?? '-' }}</td>
                                    <td>{{ $row->event_name ?? '-' }}</td>
                                    <td>{{ $row->year ?? '-' }}</td>
                                </tr>

                            @else {{-- replikasi --}}
                                <tr>
                                    <td>{{ ($innovations->currentPage()-1)*$innovations->perPage() + $loop->iteration }}</td>
                                    <td>{{ $row->innovation_title ?? '-' }}</td>
                                    <td>{{ $row->team_name ?? '-' }}</td>
                                    <td>{{ $row->theme_name ?? '-' }}</td>
                                    <td>{{ $row->category_name ?? '-' }}</td>
                                    <td>{{ $row->event_name ?? '-' }}</td>
                                    <td>{{ $row->year ?? '-' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                </tbody>
            </table>

            @if ($innovations->hasPages())
                <div class="mt-2 d-flex justify-content-end">
                    {{ $innovations->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
