@extends('layouts.app')
@section('title','Daftar Prosedur')

@section('content')
<div class="container">
  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Daftar Prosedur</h4>
    <a href="{{ route('prosedur.create') }}" class="btn btn-primary">+ Unggah Prosedur</a>
  </div>

  <form class="mb-3" method="get">
    <div class="input-group">
      <input type="text" name="q" class="form-control" value="{{ $q }}" placeholder="Cari judul...">
      <button class="btn btn-outline-secondary" type="submit">Cari</button>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered align-middle">
      <thead class="table-light">
        <tr>
          <th style="width:60px;">No</th>
          <th>Judul</th>
          <th style="width:240px;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($data as $i => $row)
          <tr>
            <td>{{ $data->firstItem() + $i }}</td>
            <td class="fw-semibold">{{ $row->title }}</td>
            <td>
               @php
                  $first = $row->files[0] ?? null;
               @endphp
                <a href="{{ route('prosedur.show',$row) }}" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                <a href="{{ route('prosedur.edit',$row) }}" class="btn btn-sm btn-warning">Update</a>
                <form action="{{ route('prosedur.destroy',$row) }}" method="post" class="d-inline"
                      onsubmit="return confirm('Hapus prosedur beserta semua file?')">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  {{ $data->links() }}
</div>
@endsection
