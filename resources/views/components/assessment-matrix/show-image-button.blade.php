@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <style>
        .image-container img {
            max-width: 100%;
            height: auto;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .image-container img:hover {
            transform: scale(1.05);
        }
    </style>
@endpush

<div>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#imageGallery" aria-expanded="false">
        Gambar Penilaian Matriks
    </button>

    <div class="collapse mt-3" id="imageGallery">
        <div class="row">
            @foreach ($assessmentMatrixImages as $image)
                <div class="col-md-3 mb-3">
                    <div class="image-container">
                        <!-- GLightbox Link -->
                        <a href="{{ asset('storage/' . $image->path) }}" class="glightbox" data-gallery="matrix-gallery"
                            data-title="Gambar Penilaian Matriks">
                            <img src="{{ asset('storage/' . $image->path) }}" class="img-fluid rounded"
                                alt="Gambar Penilaian Matriks">
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            GLightbox({
                selector: '.glightbox', // Target semua elemen dengan class `glightbox`
                touchNavigation: true,
                loop: true,
                zoomable: true,
            });
        });
    </script>
@endpush
