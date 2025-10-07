@extends('layouts.app')
@section('title', 'Registrasi Replikasi')
@push('css')
<style>
  .card-header.bg-danger, .card-header.bg-danger * { color: #fff !important; }
</style>
@endpush

@section('content')
<div class="container-xl px-4 py-4">
  <div class="card shadow-sm">
    <div class="card-header bg-danger text-white">
      <h5 class="mb-0">Registrasi Replikasi</h5>
    </div>
    <div class="card-body">
      {{-- alert flash --}}
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('replications.store', $team->id) }}">
        @csrf
        <input type="hidden" name="return_to" value="{{ $returnTo ?? url()->previous() }}">

        <div class="mb-3">
          <label class="form-label">Judul Inovasi yang akan direplikasi</label>
          <input type="text" class="form-control" value="{{ $paper->innovation_title }}" disabled>
        </div>

        <div class="mb-3">
          <label class="form-label">Nama PIC Replikator <span class="text-danger">*</span></label>
          <input type="text" name="pic_name" class="form-control" value="{{ old('pic_name', Auth::user()->name) }}" required>
          @error('pic_name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">No Handphone PIC Replikator <span class="text-danger">*</span></label>
          <input type="text" name="pic_phone" class="form-control" value="{{ old('pic_phone') }}" required>
          @error('pic_phone') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Unit Kerja Replikator</label>
          <input type="text" name="unit_name" class="form-control" value="{{ old('unit_name', Auth::user()->department_name ?? Auth::user()->group_function_name) }}">
          @error('unit_name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Atasan Replikator</label>
          <input type="text" name="superior_name" class="form-control" value="{{ old('superior_name') }}">
          @error('superior_name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Plant Replikator</label>
          <input type="text" name="plant_name" class="form-control" value="{{ old('plant_name', Auth::user()->company_name) }}">
          @error('plant_name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Area Lokasi Replikator</label>
          <input type="text" name="area_location" class="form-control" value="{{ old('area_location') }}">
          @error('area_location') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
          <label class="form-label">Tanggal Rencana Replikasi</label>
          <input type="date" name="planned_date" class="form-control" value="{{ old('planned_date') }}">
          @error('planned_date') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-danger">Ajukan</button>
          <a href="{{ $returnTo ?? url()->previous() }}" class="btn btn-outline-secondary">Batal</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
