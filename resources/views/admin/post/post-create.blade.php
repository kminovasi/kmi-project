@extends('layouts.app')
@section('title', 'Create Post')
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
                        Create Post
                    </h1>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container-xl px-4 mt-4">
    {{-- Notifikasi --}}
    <x-toast-alert type="success" message="{{ session('success') }}" />
    <x-toast-alert type="danger" message="{{ session('error') }}" />

    <div class="card mb-4">
        <div class="card-header">Create Post</div>
        <div class="card-body">
            <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data" id="postForm">
                @csrf
                <!-- Input untuk Judul -->
                <div class="mb-3">
                    <label for="title" class="form-label">Judul</label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Masukkan judul"
                        required>
                </div>

                <!-- Input untuk Gambar -->
                <div class="mb-3">
                    <label for="cover_image" class="form-label">Gambar</label>
                    <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                    <small class="text-danger d-none" id="fileError">Ukuran gambar tidak boleh lebih dari 2MB.</small>
                </div>

                <!-- Input untuk Konten -->
                <div class="mb-3">
                    <label for="content" class="form-label">Isi</label>
                    <textarea class="summernote form-control" id="content" name="content" rows="5"
                        placeholder="Masukkan isi postingan" required></textarea>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('postForm').addEventListener('submit', function (e) {
        const fileInput = document.getElementById('cover_image');
        const fileError = document.getElementById('fileError');

        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            if (file.size > 2 * 1024 * 1024) { // 2MB
                e.preventDefault();
                fileError.classList.remove('d-none');
            } else {
                fileError.classList.add('d-none');
            }
        }
    });
</script>

@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200
        });
    });
</script>
@endpush
