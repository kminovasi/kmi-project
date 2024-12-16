<section class="container py-5">
    <div class="row">
        <div class="col-sm-12 col-md-3 mb-3 mb-md-0 rounded shadow" style="background-color: #a00000">
            <header class=" text-white p-4">
                <h2 class="display-5 fw-bold mt-3">Update Terbaru</h2>
                <p class="lead text-white mt-3">
                    Temukan berita terkini dari kami, termasuk pengumuman patut diketahui !
                </p>
                <button href="#" class="btn btn-warning shadow"> Lihat berita lainnya <i
                        class="fa-solid fa-arrow-right"></i></button>
            </header>
        </div>
        <div class="col-12 col-md-9 p-2">
            <div class="owl-carousel">
                {{-- item 1 --}}
                <div class="item">
                    <div class="p-2 border rounded-2">
                        <img class="img-fluid rounded-2" src="{{ asset('assets/login-frame.png') }}" alt="">
                        <div class="p-2">
                            <h5>Lorem Ipsum dolor shit amet eh askdj iewur nf jsdkfj sdflkj sdflkj sldfkj weiru </h5>
                        </div>
                    </div>
                </div>
                {{-- item 2 --}}
                <div class="item">
                    <div class="p-2 border rounded-2">
                        <img class="img-fluid rounded-2" src="{{ asset('assets/login-frame.png') }}" alt="">
                        <div class="p-2">
                            <h5>Lorem Ipsum dolor shit amet eh askdj iewur nf jsdkfj sdflkj sdflkj sldfkj weiru </h5>
                        </div>
                    </div>
                </div>
                {{-- item 3 --}}
                <div class="item">
                    <div class="p-2 border rounded-2">
                        <img class="img-fluid rounded-2" src="{{ asset('assets/login-frame.png') }}" alt="">
                        <div class="p-2">
                            <h5>Lorem Ipsum dolor shit amet eh askdj iewur nf jsdkfj sdflkj sdflkj sldfkj weiru </h5>
                        </div>
                    </div>
                </div>
                {{-- item 4 --}}
                <div class="item">
                    <div class="p-2 border rounded-2">
                        <img class="img-fluid rounded-2" src="{{ asset('assets/login-frame.png') }}" alt="">
                        <div class="p-2">
                            <h5>Lorem Ipsum dolor shit amet eh askdj iewur nf jsdkfj sdflkj sdflkj sldfkj weiru </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('js')
<script>
    $(document).ready(function(){
    $(".owl-carousel").owlCarousel({
        items: 3, // Jumlah item yang ditampilkan
        loop: true, // Mengulangi carousel
        margin: 10, // Jarak antar item
        autoplay: true, // Mengaktifkan autoplay
        autoplayTimeout: 3000, // Waktu antara setiap slide
        autoplayHoverPause: true // Pause saat hover
    });
});
</script>
@endpush
