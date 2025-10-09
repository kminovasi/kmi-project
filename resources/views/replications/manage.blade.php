@extends('layouts.app')
@section('title','Kelengkapan Replikasi')

@section('content')
@php
  // Superadmin = selalu read-only; creator = bisa edit
  $readOnly = auth()->user()->role === 'Superadmin' ? true : (auth()->id() !== $replication->created_by);
@endphp

<div class="container-xl px-4 py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Kelengkapan Replikasi – {{ $replication->innovation_title }}</h4>
    <a href="{{ route('replications.index') }}" class="btn btn-sm btn-light">Kembali</a>
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
  @endif

  <div class="card">
    <div class="card-body">
      <form method="POST"
            action="{{ $readOnly ? '' : route('replications.manage.update', $replication->id) }}"
            enctype="multipart/form-data"
            @if($readOnly) onsubmit="return false;" @endif>
        @csrf

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Benefit Finansial (Rp)</label>
            <input type="number" name="financial_benefit" min="0" step="1" class="form-control"
                   value="{{ old('financial_benefit', $replication->financial_benefit) }}"
                   {{ $readOnly ? 'disabled' : '' }}>
          </div>
          <div class="col-md-4">
            <label class="form-label">Benefit Potensial (Rp)</label>
            <input type="number" name="potential_benefit" min="0" step="1" class="form-control"
                   value="{{ old('potential_benefit', $replication->potential_benefit) }}"
                   {{ $readOnly ? 'disabled' : '' }}>
          </div>
        </div>

        <hr>

        @unless($readOnly)
          <div class="mb-3">
            <label class="form-label">Unggah Dokumen (bisa pilih banyak)</label>
            <input type="file" name="files[]" class="form-control" multiple
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
            <div class="form-text">Max 20MB per berkas.</div>
          </div>
        @endunless

        @php $disk = 'public'; @endphp
        @if(!empty($replication->files))
        <div class="mb-2"><strong>File tersimpan:</strong></div>

        @php
            $files = collect($replication->files ?? [])->sortByDesc('uploaded_at');
        @endphp

        <ul class="list-group mb-3">
            @foreach($files as $f)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div class="me-2">
                <div>{{ $f['name'] ?? basename($f['path'] ?? '') }}</div>
                <small class="text-muted">
                    {{ $f['mime'] ?? 'file' }},
                    {{ isset($f['size']) ? number_format($f['size']/1024, 0) . ' KB' : '' }}
                    @if(!empty($f['uploaded_at']))
                    — diunggah {{ \Carbon\Carbon::parse($f['uploaded_at'])->format('d M Y H:i') }}
                    @endif
                </small>
                </div>

            @php
              // URL-safe base64 (avoid + / = issues in query string)
              $raw = $f['path'] ?? '';
              $b64 = $raw
                ? rtrim(strtr(base64_encode($raw), '+/', '-_'), '=')
                : null;
            @endphp
            
            @if($b64)
              <a href="{{ route('replications.file', ['replication' => $replication->id, 'p' => $b64]) }}"
                 target="_blank" class="btn btn-sm btn-outline-secondary">
                Lihat
              </a>
            @else
              <span class="text-danger small">File tidak ditemukan</span>
            @endif

            </li>
            @endforeach
        </ul>
        @endif

        <div class="d-flex gap-2">
          @unless($readOnly)
            <button type="submit" class="btn btn-danger">Simpan</button>
          @endunless
          {{-- <a href="{{ route('replications.index') }}" class="btn btn-outline-secondary">Kembali</a> --}}
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
