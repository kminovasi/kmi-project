@extends('layouts.app')
@section('title', 'Certificate')
@section('content')
@push('css')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
@endpush

<header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
    <div class="container-xl px-4">
        <div class="page-header-content">
            <div class="row align-items-center justify-content-between pt-3">
                <div class="col-auto mb-3">
                    <h1 class="page-header-title">
                        <div class="page-header-icon"><i data-feather="image"></i></div>
                        Template Sertifikat
                    </h1>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container-xl px-4 mt-4">
    <div class="mb-3">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
            {{ session('success') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        @if (session('errors'))
        <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
            {{ session('errors') }}
            <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-end">
            <button type="button" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#uploadModal">
                Upload Sertifikat
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">No</th>
                            <th scope="col">Event</th>
                            <th scope="col">Template Gambar</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Contoh data sertifikat -->
                        @foreach($certificates as $key => $certificate)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $certificate->event->event_name }} {{ $certificate->event->year }}</td>
                            <td>
                                <img src="{{ asset('storage/'.$certificate->template_path) }}" alt="Template Gambar"
                                    class="img-fluid" style="max-width: 100px; height: auto;">
                            </td>
                            <td>
                                <form action="{{ route('certificates.destroy', $certificate->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus sertifikat ini?')">
                                        <i data-feather="trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('certificates.store') }}" enctype="multipart/form-data" method="POST">
            @method('post')
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="uploadModalLabel">Upload Sertifikat</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="event_id" class="form-label">Event</label>
                        <select class="form-select" name="event_id" id="event_id" required>
                            <option value="">Pilih Event</option>
                            @foreach($eventsWithoutCertificate as $event)
                                <option value="{{ $event->event_id }}">{{ $event->event_name }} {{ $event->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="template" class="form-label">Upload Gambar Sertifikat</label>
                        <input type="file" class="form-control" name="template" id="template" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>



@endsection
