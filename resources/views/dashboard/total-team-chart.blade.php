@extends('layouts.app')
@section('title', 'Chart Total Tim | Dashboard')

@section('content')
  <div class="container mt-3">
    <div class="card">
      <div class="card-header" style="background-color:#eb4a3a">
        <h5 class="text-white">Chart Total Inovasi Di Tiap Perusahaan</h5>
      </div>
      <div class="card-body">
        <canvas id="total-team-chart"></canvas>
      </div>
    </div>

    {{-- Expose data ke global agar bisa dibaca module JS --}}
    <script>
      window.chartDataTotalTeam = @json($chartDataTotalTeam);
    </script>

    {{-- Optional: komponen lain --}}
    <x-dashboard.total-company-innovator-chart />
  </div>
@endsection

{{-- Pilih salah satu. Saya pakai file build hashed yang kamu punya --}}
<script src="{{ asset('build/assets/totalTeamChart-0f8fbc35.js') }}" type="module"></script>
