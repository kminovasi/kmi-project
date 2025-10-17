@extends('layouts.app')
@section('title','Detail Prosedur')

@section('content')
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Detail Prosedur</h4>
    <a href="{{ route('prosedur.index') }}" class="btn btn-light">Kembali</a>
  </div>

  <div class="card mb-3">
    <div class="card-body">
      <div class="mb-2"><span class="text-muted">Judul:</span> <span class="fw-semibold">{{ $prosedur->title }}</span></div>
      <div class="text-muted small">
        Dibuat: {{ $prosedur->created_at?->format('d M Y H:i') }} |
        Diperbarui: {{ $prosedur->updated_at?->format('d M Y H:i') }}
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header fw-semibold">Lampiran</div>
    <div class="card-body p-0">
      <ul class="list-group list-group-flush">
        @forelse($prosedur->files as $i => $f)
          @php
            $nama = $f['name'] ?? basename($f['path'] ?? '');
            $mime = $f['mime'] ?? '-';
            $size = isset($f['size']) ? number_format($f['size']/1024,1).' KB' : '-';
          @endphp
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold">{{ $nama }}</div>
              <div class="text-muted small">{{ $mime }} • {{ $size }}</div>
            </div>
            <a class="btn btn-sm btn-primary" target="_blank"
               href="{{ route('prosedur.getfile', [$prosedur, $i]) }}">
              Buka
            </a>
          </li>
        @empty
          <li class="list-group-item text-muted">Belum ada lampiran.</li>
        @endforelse
      </ul>
    </div>
  </div>

  <div class="mt-3">
    <a href="{{ route('prosedur.edit',$prosedur) }}" class="btn btn-warning">Update</a>
    <form class="d-inline" method="POST" action="{{ route('prosedur.destroy',$prosedur) }}"
          onsubmit="return confirm('Hapus prosedur beserta semua file?')">
      @csrf @method('DELETE')
      <button class="btn btn-danger">Delete</button>
    </form>
  </div>
</div>
@endsection
