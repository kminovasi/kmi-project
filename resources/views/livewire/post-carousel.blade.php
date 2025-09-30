<div>
    <section class="container py-5">
        <div class="row">
            <div class="col-sm-12 col-md-4 mb-3 mb-md-0 rounded shadow" style="background-color: #a00000">
                <header class=" text-white p-4">
                    <h2 class="display-5 fw-bold mt-3">Update Terbaru</h2>
                    <p class="lead text-white">
                        Temukan update terkini dari kami !
                    </p>
                    <a href="{{ route('post.list') }}" class="btn btn-warning shadow"> Lihat berita lainnya <i
                            class="fa-solid fa-arrow-right"></i></a>
                </header>
            </div>
            <div class="col-12 col-md-8 p-2">
                <div class="owl-carousel">
                    @foreach ($posts as $post )
                    <div class="item">
                        <div class="p-2 border rounded-2">
                            <img class="img-fluid rounded-2" src="{{ route('query.getFile') }}?directory={{ urlencode($post->cover_image) }}"
                                alt="post image" style="width: 100%; height: auto; aspect-ratio: 4 / 3;">
                            <div class="p-2">
                                <a href="{{ route('post.show', $post->slug) }}" class="text-decoration-none text-dark"><h4>{{ $post->title }}</h4></a>
                                <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>

    @push('js')
    <script>
        $(document).ready(function(){
        $(".owl-carousel").owlCarousel({
            items: 2, // Jumlah item yang ditampilkan
            loop: false, // Mengulangi carousel
            margin: 10, // Jarak antar item
            autoplay: true, // Mengaktifkan autoplay
            autoplayTimeout: 2000, // Waktu antara setiap slide
            autoplayHoverPause: true // Pause saat hover
        });
    });
    </script>
    @endpush
</div>



