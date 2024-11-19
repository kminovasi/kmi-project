@extends('layouts.app')
@section('title', 'Management Penilaian Matriks')

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <style>
        .image-container {
            position: relative;
            cursor: pointer;
        }

        .zoom-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2rem;
            color: white;
            display: none;
        }

        .image-container:hover .zoom-icon {
            display: block;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Manajemen Gambar Matriks Penilaian</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                        <i class="fas fa-upload"></i> Unggah Gambar
                    </button>
                </div>

                <div class="row" id="imageGrid">
                    @foreach ($images as $image)
                        <div class="col-md-3 mb-3 image-item" data-id="{{ $image->id }}">
                            <div class="card">
                                <div class="image-container">
                                    <!-- Tambahkan data-gallery -->
                                    <a href="{{ asset('storage/' . $image->path) }}" class="glightbox"
                                        data-gallery="image-gallery" data-title="Gambar Matriks" data-zoomable="true"
                                        zoomable="true">
                                        <img src="{{ asset('storage/' . $image->path) }}" class="card-img-top"
                                            alt="Gambar Matriks">
                                        <div class="zoom-icon">
                                            <i class="fas fa-search-plus"></i>
                                        </div>
                                    </a>
                                </div>
                                <div class="card-body text-center">
                                    <form action="{{ route('management-system.assessment-matrix.destroy') }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $image->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm delete-image">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>



                {{ $images->links() }}
            </div>
        </div>

        <!-- Modal Upload -->
        <div class="modal fade" id="uploadModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Unggah Gambar Matriks</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('management-system.assessment-matrix.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Pilih Gambar</label>
                                <input type="file" class="form-control" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Unggah</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        // Inisialisasi GLightbox
        const lightbox = GLightbox({
            touchNavigation: true,
            loop: true,
            zoomable: true,
        });
    </script>
@endpush
