@extends('layouts.app')
@section('title', 'Manajemen Sistem | Matriks Penilaian')

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
        <div class="card-header text-center bg-light">
            <h5 class="card-title fw-bold mb-0">Manajemen Gambar Matriks Penilaian</h5>
        </div>

        <div class="card-body">
            <div class="mb-3 text-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <span class="d-flex align-items-center gap-2">
                        <i class="fas fa-upload"></i>
                        Unggah Gambar
                    </span>
                </button>
            </div>


            <div class="row g-4" id="imageGrid">
                @foreach ($images as $image)
                <div class="col-md-3 image-item" data-id="{{ $image->id }}">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="image-container position-relative overflow-hidden rounded-top">
                            <a href="{{ asset('storage/' . $image->path) }}" class="glightbox" data-gallery="image-gallery" data-title="Gambar Matriks" data-zoomable="true">
                                <img src="{{ asset('storage/' . $image->path) }}" class="card-img-top" alt="Gambar Matriks">
                                <div class="zoom-icon position-absolute top-50 start-50 translate-middle bg-dark text-white rounded-circle p-2 opacity-75">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </a>
                        </div>
                        <div class="card-body text-center">
                            <form action="{{ route('management-system.assessment-matrix.destroy') }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="id" value="{{ $image->id }}">
                                <button type="button" class="btn btn-danger btn-sm d-flex align-items-center justify-content-center gap-2 delete-image position-absolute bottom-0 start-50 translate-middle-x mb-2" onclick="confirmDelete(this)">
                                    <i class="fas fa-trash"></i>
                                    <span>Hapus</span>
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
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header">
                    <h5 class="modal-title">Unggah Gambar Matriks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('management-system.assessment-matrix.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf
                        <div class="mb-4">
                            <label for="imageUpload" class="form-label fw-semibold">Pilih Gambar</label>
                            <input type="file" id="imageUpload" class="form-control form-control-lg" name="image" accept=".png,.jpg,.jpeg" required>
                            <div class="form-text text-muted">
                                Hanya file dengan format <strong>.png</strong> dan <strong>.jpg</strong> yang dapat diunggah.
                            </div>
                            <div class="invalid-feedback">
                                Harap pilih file gambar dengan format yang valid.
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-danger me-2" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Unggah</button>
                        </div>
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
        touchNavigation: true
        , loop: true
        , zoomable: true
    , });

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Apakah Anda yakin?'
            , text: "Tindakan ini tidak dapat dibatalkan!"
            , icon: 'warning'
            , showCancelButton: true
            , confirmButtonColor: '#d33'
            , cancelButtonColor: '#6c757d'
            , confirmButtonText: 'Ya, hapus!'
            , cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Akses form terkait tombol yang ditekan dan submit
                const form = button.closest('form');
                form.submit();
            }
        });
    }
</script>
@endpush
