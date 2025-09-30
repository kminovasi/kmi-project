@extends('layouts.app')
@section('title', 'Daftar Sertifikat Juri')

@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
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
            <div class="page-header-icon"><i data-feather="award"></i></div>
            Daftar Sertifikat Juri
          </h1>
        </div>
      </div>
    </div>
  </div>
</header>

<div class="container-xl px-4">
  <div class="card mb-4">
    <div class="card-body">

      {{-- FILTER Event (auto submit) --}}
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
      </form>

      <table class="table table-bordered align-middle" id="judgeTable" style="width:100%">
        <thead>
          <tr>
            <th style="width:60px">No</th>
            <th>Nama Juri</th>
            <th>Perusahaan</th>
            <th>Event</th>
            <th style="width:220px">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($judges as $index => $judge)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $judge->name }}</td>
            <td>{{ $judge->company_name }}</td>
            <td>{{ $judge->event_title }} Tahun {{ $judge->event_year }}</td>
            <td>
              <form action="{{ route('cv.generateCertificate') }}" method="POST">
                @csrf
                <input type="hidden" name="employee" value="{{ $judge->employee_id }}">
                <input type="hidden" name="event_id" value="{{ $judge->event_id }}">
                <input type="hidden" name="certificate_type" value="judge">

                @if(Auth::user()->role == 'Superadmin')
                  <button type="submit" class="btn btn-sm btn-info">
                    <i data-feather="download"></i> Sertifikat Juri
                  </button>
                @else
                  <button type="button" class="btn btn-sm btn-secondary" disabled>
                    <i data-feather="download"></i> Sertifikat Juri
                  </button>
                @endif
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      @push('js')
      <script>
        // Auto-submit filter event (tanpa tombol)
        (function(){
          const form     = document.getElementById('filterForm');
          const eventSel = form.querySelector('select[name="event_id"]');
          const submitForm = () => {
            const url = new URL(window.location.href);
            url.searchParams.delete('page'); // reset pagination
            url.searchParams.set('event_id', eventSel.value || '');
            window.location.assign(url.toString());
          };
          eventSel.addEventListener('change', submitForm);
        })();

        // DataTables (10/baris + search bawaan)
        $(function () {
          $('#judgeTable').DataTable({
            pageLength: 10,
            lengthChange: false,
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
