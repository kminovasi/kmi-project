@extends('layouts.app')
@section('title','Perbarui Prosedur')

@push('css')
<style>
  .page-wrap { max-width: 980px; }
    @media (min-width: 992px){
      .page-wrap { margin-left: 8px; }
  }

  .section-title{
    font-weight: 600; font-size: .95rem; color:#6b7280; 
    text-transform: uppercase; letter-spacing:.04em; margin-bottom:.5rem;
  }

  .file-meta{ font-size: .8rem; color:#6b7280; }
  .list-group-item .btn-icon{ padding: .25rem .5rem; }

  .addfile-header{ display:flex; justify-content:space-between; align-items:center; }
  .dropzone-like{
    border:1px dashed #cbd5e1; border-radius:.5rem; padding:.75rem .75rem;
    background:#f8fafc;
  }

  .file-row .form-control{ border-top-right-radius:0; border-bottom-right-radius:0; }
  .file-row .btnRemove{ border-top-left-radius:0; border-bottom-left-radius:0; }

  .action-bar{
    position: sticky; bottom: 0; z-index: 5;
    background: #fff; padding: .75rem; border-top:1px solid #e5e7eb;
    display:flex; gap:.5rem;
  }
</style>
@endpush

@section('content')
<div class="container page-wrap">

  <h4 class="mb-3">Update Prosedur</h4>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
      <div class="fw-bold">Gagal memperbarui:</div>
      <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
    </div>
  @endif

  <form id="formUpdate" action="{{ route('prosedur.update',$prosedur) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="card mb-3">
      <div class="card-body">
        <div class="section-title">Informasi Utama</div>
        <div class="mb-3">
          <label class="form-label">Judul Prosedur <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" value="{{ old('title',$prosedur->title) }}" required>
        </div>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        <div class="section-title">Lampiran Eksisting</div>
        <ul class="list-group">
          @forelse($prosedur->files as $i => $f)
            @php
              $nama = $f['name'] ?? basename($f['path'] ?? '');
              $mime = $f['mime'] ?? null;
              $size = isset($f['size']) ? number_format($f['size']/1024,1).' KB' : null;
            @endphp
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div class="me-3">
                <a class="fw-semibold text-decoration-none" target="_blank"
                   href="{{ route('prosedur.getfile', [$prosedur, $i]) }}">
                  {{ $nama }}
                </a>
                @if($mime || $size)
                  <div class="file-meta">{{ $mime ?? '-' }} @if($mime && $size) • @endif {{ $size ?? '-' }}</div>
                @endif
              </div>
              <button type="submit" class="btn btn-outline-danger btn-sm btn-icon"
                      form="del-file-{{ $i }}"
                      title="Hapus file"
                      onclick="return confirm('Hapus file ini?')">
                Hapus
              </button>
            </li>
          @empty
            <li class="list-group-item text-muted">Tidak ada file.</li>
          @endforelse
        </ul>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <div class="addfile-header mb-2">
          <div class="section-title mb-0">Tambah File Baru (opsional)</div>
          <button class="btn btn-sm btn-outline-primary" type="button" id="btnAddFile">+ Tambah File</button>
        </div>

        <div class="dropzone-like mb-2">
          <div id="fileList">
            <div class="input-group mb-2 file-row">
              <input type="file" name="files[]" class="form-control">
              <button type="button" class="btn btn-outline-danger btnRemove">Hapus</button>
            </div>
          </div>
          <div class="text-muted small">Format umum: PDF, DOCX, XLSX, JPG, PNG. Maks 10 MB per file.</div>
        </div>
      </div>
    </div>

    <div class="action-bar rounded-bottom">
      <button class="btn btn-success" type="submit">Simpan Perubahan</button>
      <a href="{{ route('prosedur.index') }}" class="btn btn-light">Batal</a>
    </div>
  </form>

  @foreach($prosedur->files as $i => $f)
    <form id="del-file-{{ $i }}" method="POST"
          action="{{ route('prosedur.files.destroy', [$prosedur, $i]) }}"
          style="display:none">
      @csrf @method('DELETE')
    </form>
  @endforeach
</div>

@push('js')
  <script>
    (function() {
      const btnAdd = document.getElementById('btnAddFile');
      const list = document.getElementById('fileList');

      if (btnAdd && list) {
        btnAdd.addEventListener('click', function () {
          const row = document.createElement('div');
          row.className = 'input-group mb-2 file-row';
          row.innerHTML = `
            <input type="file" name="files[]" class="form-control">
            <button type="button" class="btn btn-outline-danger btnRemove">Hapus</button>
          `;
          list.appendChild(row);
        });
      }

      document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btnRemove')) {
          const row = e.target.closest('.file-row');
          if (row) row.remove();
        }
      });
    })();
  </script>
@endpush
@endsection
