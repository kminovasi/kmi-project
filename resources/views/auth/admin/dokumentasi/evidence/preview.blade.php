@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">{{ $paper->innovation_title }}</h4>

    <!-- Loading spinner -->
    <div id="loading-spinner" style="text-align:center; padding:20px;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Memuat file...</p>
    </div>

    <!-- PDF viewer -->
    <div id="pdf-viewer" style="border:1px solid #ccc; height:800px; overflow-y:auto; display:none;"></div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<script>
const url = @json($fileUrl);

const pdfjsLib = window['pdfjs-dist/build/pdf'];
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

// Tampilkan loading spinner
document.getElementById('loading-spinner').style.display = 'block';

const loadingTask = pdfjsLib.getDocument(url);
loadingTask.promise.then(pdf => {
    const viewer = document.getElementById('pdf-viewer');

    let renderedPages = 0;

    for(let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
        pdf.getPage(pageNum).then(page => {
            const scale = 1.5;
            const viewport = page.getViewport({ scale });

            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            viewer.appendChild(canvas);

            page.render({ canvasContext: context, viewport }).promise.then(() => {
                renderedPages++;

                // Jika semua halaman sudah selesai dirender, sembunyikan loading
                if (renderedPages === pdf.numPages) {
                    document.getElementById('loading-spinner').style.display = 'none';
                    viewer.style.display = 'block';
                }
            });
        });
    }
}).catch(error => {
    console.error('Error loading PDF:', error);
    alert('Gagal memuat PDF');
    document.getElementById('loading-spinner').innerHTML = "<p class='text-danger'>Gagal memuat dokumen.</p>";
});
</script>
@endsection
