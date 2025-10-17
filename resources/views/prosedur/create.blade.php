@extends('layouts.app')
@section('title','Pengajuan Prosedur')

@section('content')
<div class="container">
  <h4 class="mb-3">Pengajuan Prosedur</h4>

  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold">Gagal menyimpan:</div>
      <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
    </div>
  @endif

  <form action="{{ route('prosedur.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
      <label class="form-label">Judul Prosedur <span class="text-danger">*</span></label>
      <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
    </div>

    <div class="mb-2 d-flex justify-content-between align-items-center">
      <label class="form-label mb-0">Lampiran</label>
      {{-- <button class="btn btn-sm btn-outline-primary" type="button" id="btnAddFile">+ Tambah File</button> --}}
    </div>

    <div id="fileList">
      <div class="input-group mb-2 file-row">
        <input type="file" name="files[]" class="form-control" />
        <button type="button" class="btn btn-outline-danger btnRemove">Hapus</button>
      </div>
    </div>

    <div class="mt-3">
      <button class="btn btn-success" type="submit">Simpan</button>
      <a href="{{ route('prosedur.index') }}" class="btn btn-light">Batal</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
(function () {
  function addFileRow() {
    const wrapper = document.getElementById('fileList');
    if (!wrapper) return;
    const row = document.createElement('div');
    row.className = 'input-group mb-2 file-row';
    row.innerHTML = `
      <input type="file" name="files[]" class="form-control" />
      <button type="button" class="btn btn-outline-danger btnRemove">Hapus</button>
    `;
    wrapper.appendChild(row);
  }

  document.addEventListener('click', function (e) {
    if (e.target && e.target.id === 'btnAddFile') {
      e.preventDefault();
      addFileRow();
    }
    if (e.target && e.target.classList.contains('btnRemove')) {
      const row = e.target.closest('.file-row');
      if (row) row.remove();
    }
  });
})();
</script>
@endpush

@endsection
