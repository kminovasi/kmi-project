@extends('layouts.app')
@section('title', 'Daftar Sertifikat Peserta & Tim')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
  /* rapikan baris filter */
  .filter-bar .form-select { height: 42px; }
</style>
@endpush
@push('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
  <div class="container-xl px-4">
    <div class="page-header-content">
      <div class="row align-items-center justify-content-between pt-3">
        <div class="col-auto mb-3">
          <h1 class="page-header-title">
            <div class="page-header-icon"><i data-feather="users"></i></div>
            Daftar Sertifikat Peserta & Tim
          </h1>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="container-xl px-4">
  <div class="card mb-4">
    <div class="card-body">

      {{-- FILTERS (auto submit) --}}
      <form id="filterForm" method="GET" class="row g-2 filter-bar mb-3">
        <div class="col-md-6">
          <select name="event_id" class="form-select">
            <option value="">-- Pilih Event --</option>
            @foreach($events as $e)
              <option value="{{ $e->id }}" {{ (string)$e->id === (string)$eventId ? 'selected' : '' }}>
                {{ $e->event_name }} {{ $e->year }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <select name="category_id" class="form-select">
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}" {{ (string)$c->id === (string)$categoryId ? 'selected' : '' }}>
                {{ $c->category_name }}
              </option>
            @endforeach
          </select>
        </div>
      </form>

    <table class="table table-bordered align-middle" id="participantsTable" style="width:100%">
        <thead>
          <tr>
            <th style="width:60px">No</th>
            <th>Event</th>
            <th style="width:90px">Tahun</th>
            <th>Tim</th>
            <th>Perusahaan</th>
            <th>Kategori</th>
            <th style="width:140px">Status</th>
            <th style="width:260px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($teams as $i => $t)
            @php
              // payload ZIP Peserta
              $inovasiParticipant = [
                'event_id'     => $t->event_id,
                'team_id'      => $t->team_id,
                'team_name'    => $t->team_name,
                'company_name' => $t->company_name,
                'category'     => $t->category,
                'certificate'  => $t->certificate,
                'event_end'    => $t->event_end,
                'badge_rank_1' => $t->badge_rank_1 ?? null,
                'badge_rank_2' => $t->badge_rank_2 ?? null,
                'badge_rank_3' => $t->badge_rank_3 ?? null,
              ];

              // payload Sertifikat Tim (single)
              $inovasiTeam = [
                'event_id'         => $t->event_id,
                'team_id'          => $t->team_id,
                'team_name'        => $t->team_name,
                'company_name'     => $t->company_name,
                'category'         => $t->category,
                'certificate'      => $t->certificate,
                'event_end'        => $t->event_end,
                'keputusan_bod'    => $t->keputusan_bod,
                'innovation_title' => $t->innovation_title ?? $t->team_name,
                'badge_rank_1'     => $t->badge_rank_1 ?? null,
                'badge_rank_2'     => $t->badge_rank_2 ?? null,
                'badge_rank_3'     => $t->badge_rank_3 ?? null,
              ];

              $badgeHtml = '';
              if ((int)$t->is_best_of_the_best === 1) {
                  $badgeHtml = '<span class="badge bg-warning text-dark">Best of The Best</span>';
              } elseif ((int)$t->is_honorable_winner === 1) {
                  $badgeHtml = '<span class="badge bg-info text-dark">Juara Harapan</span>';
              } elseif ((int)$t->is_honorable_winner === 1) {
                  $badgeHtml = '<span class="badge bg-info text-dark">'.e($t->keputusan_bod).'</span>';
              } elseif ((int)$t->team_rank >= 1 && (int)$t->team_rank <= 3) {
                  $badgeHtml = '<span class="badge bg-success">Juara '.(int)$t->team_rank.'</span>';
              } else {
                  $badgeHtml = '<span class="badge bg-secondary">Peserta</span>';
              }
            @endphp
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>{{ $t->event_name }}</td>
              <td>{{ $t->year }}</td>
              <td>{{ $t->team_name }}</td>
              <td>{{ $t->company_name }}</td>
              <td>{{ $t->category }}</td>
              <td>{!! $badgeHtml !!}</td>
              <td class="d-flex flex-wrap gap-2">
                {{-- ZIP Peserta (per tim) --}}
                <form action="{{ route('cv.generateCertificate') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="certificate_type" value="participant">
                  <input type="hidden" name="inovasi" value='@json($inovasiParticipant)'>
                  <button type="submit" class="btn btn-sm btn-info">
                    <i data-feather="download"></i> Sertifikat Peserta (ZIP)
                  </button>
                </form>

                {{-- Sertifikat Tim (single PDF) --}}
                <form action="{{ route('cv.generateCertificate') }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="certificate_type" value="team">
                  <input type="hidden" name="inovasi" value='@json($inovasiTeam)'>
                  <input type="hidden" name="team_rank" value="{{ (int)$t->team_rank ?: null }}">
                  <button type="submit" class="btn btn-sm btn-success">
                    <i data-feather="award"></i> Sertifikat Tim
                  </button>
                </form>

                {{-- Sertifikat Best of The Best--}}
                  @if((int)$t->is_best_of_the_best === 1)
                    <form action="{{ route('cv.generateCertificate') }}" method="POST" class="d-inline">
                      @csrf
                      <input type="hidden" name="certificate_type" value="best_of_the_best">
                      <input type="hidden" name="inovasi" value='@json($inovasiTeam)'>
                      <button type="submit" class="btn btn-sm btn-warning text-dark">
                        <i data-feather="star"></i> Best of The Best
                      </button>
                    </form>
                  @endif

                  {{-- Sertifikat Juara Harapan --}}
                  @if((int)$t->is_honorable_winner === 1)
                    <form action="{{ route('cv.generateCertificate') }}" method="POST" class="d-inline">
                      @csrf
                      <input type="hidden" name="certificate_type" value="honorable_winner">
                      <input type="hidden" name="inovasi" value='@json($inovasiTeam)'>
                      <button type="submit" class="btn btn-sm btn-primary">
                        <i data-feather="award"></i> Juara Harapan
                      </button>
                    </form>
                  @endif

                  {{-- Sertifikat Keputusan BOD--}}
                  @if((int)$t->keputusan_bod)
                    <form action="{{ route('cv.generateCertificate') }}" method="POST" class="d-inline">
                      @csrf
                      <input type="hidden" name="certificate_type" value="keputusan_bod">
                      <input type="hidden" name="inovasi" value='@json($inovasiTeam)'>
                      <button type="submit" class="btn btn-sm btn-primary">
                        <i data-feather="award"></i>({{ ucwords(strtolower($t->keputusan_bod)) }})
                      </button>
                    </form>
                  @endif
                  
                 {{-- Sertifikat Keputusan BOD--}}
                 @if (!empty($t->keputusan_bod))
                    <form action="{{ route('cv.generateCertificate') }}" method="POST" class="d-inline">
                      @csrf
                      <input type="hidden" name="certificate_type" value="keputusan_bod">
                      <input type="hidden" name="inovasi" value='@json($inovasiTeam)'>
                      <button type="submit" class="btn btn-sm btn-primary">
                        <i data-feather="award"></i>({{ ucwords(strtolower($t->keputusan_bod)) }})
                      </button>
                    </form>
                  @endif
                  
                </td>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      @push('js')
      <script>
        // Auto-submit filter (tanpa tombol)
        (function(){
          const form     = document.getElementById('filterForm');
          const eventSel = form.querySelector('select[name="event_id"]');
          const catSel   = form.querySelector('select[name="category_id"]');

          const submitForm = () => {
            const url = new URL(window.location.href);
            url.searchParams.delete('page'); 
            url.searchParams.set('event_id', eventSel.value || '');
            url.searchParams.set('category_id', catSel.value || '');
            window.location.assign(url.toString());
          };

          eventSel.addEventListener('change', submitForm);
          catSel.addEventListener('change', submitForm);
        })();

        $(function () {
          $('#participantsTable').DataTable({
            pageLength: 10,
            lengthChange: false,
            order: [[1, 'desc'], [3, 'asc']],
            language: { searchPlaceholder: 'Cari di tabel…', search: '' }
          });
          if (window.feather) feather.replace();
        });
      </script>
      @endpush

    </div>
  </div>
</div>
@endsection
