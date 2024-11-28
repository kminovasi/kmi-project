@extends('layouts.guest')
@section('title', 'home')

@section('content')
    {{-- Hero Section --}}
    @include('homepage.hero-section')

    {{-- <section class="py-5">
        <div id="content" class="pt-5">
            <div class="mb-4" id="pendaftaran-title">
                <h2 class="color-main animated-title">Timeline Kompetisi Inovasi</h2>
                <hr class="custom-hr">
            </div>
            <ul class="timeline mx-auto">
                @foreach ($timeline as $t)
                    <li class="event animated-item"
                        data-date="{{ \Carbon\Carbon::parse($t->tanggal_mulai)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($t->tanggal_selesai)->format('d M Y') }}
                            ">
                        <h3>{{ $t->judul_kegiatan }} </h3>
                        <p>{{ $t->deskripsi }} </p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section> --}}

    <section class="container py-5">
        <div class="row g-0">
            <div class="col-md-6">
                <img class="img-fluid rounded-start" src="{{ asset('assets/login-frame.png') }}" alt="content_1">
            </div>
            <div class="col-md-6 p-5 text-white rounded-end" style="background-color: #a00000">
                <h2 class="display-5 fw-bold text-white mb-3 fst-italic">
                    <blockquote>"Empowering Innovators Through Awards"</blockquote>
                </h2>
                <p class="lead">
                    Innovation Award adalah ajang tahunan SIG untuk mengapresiasi inovator yang telah membawa kemajuan nyata
                    bagi perusahaan. Kegiatan ini mewadahi kreativitas karyawan dalam menciptakan solusi inovatif yang telah
                    terverifikasi dan memberikan dampak nyata.
                </p>
            </div>
        </div>
    </section>

    {{-- Timeline --}}
    <section class="container py-5">
        <header class="mb-4">
            <h2 class="display-5 fw-bold text-danger mt-3">Timeline Event</h2>
            <p class="lead text-muted mt-3">
                Catat tanggal-tanggal penting dan jangan lewatkan setiap momen!
            </p>
        </header>
        <div class="py-4 mb-4">

            <!-- timeline item 1 -->
            <div class="row no-gutters">
                <div class="col-sm"> <!--spacer--> </div>
                <!-- timeline item 1 center dot -->
                <div class="col-sm-1 text-center flex-column d-none d-sm-flex">
                    <div class="row h-50">
                        <div class="col">&nbsp;</div>
                        <div class="col">&nbsp;</div>
                    </div>
                    <h5 class="m-2">
                        <span class="badge rounded-circle bg-danger">&nbsp;</span>
                    </h5>
                    <div class="row h-50">
                        <div class="col border-end">&nbsp;</div>
                        <div class="col">&nbsp;</div>
                    </div>
                </div>
                <!-- timeline item 1 event content -->
                <div class="col-sm py-2">
                    <div class="card border-danger shadow-sm">
                        <div class="card-body">
                            <div class="float-end text-danger small">Jan 9th 2024 7:00 AM</div>
                            <h4 class="card-title text-danger">Internal Innovation Event 2024</h4>
                            <p class="card-text">
                                Event lomba internal 2024 3000 7000.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- timeline item 2 -->
            <div class="row no-gutters">
                {{-- timeline item 2 even content --}}
                <div class="col-sm py-2">
                    <div class="card border-danger shadow-sm">
                        <div class="card-body">
                            <div class="float-end text-danger small">Okt 10th 2024 8:30 AM</div>
                            <h4 class="card-title text-danger">Innovation Event Group 2024</h4>
                            <p class="card-text">
                                Event inovasi grup</p>
                        </div>
                    </div>
                </div>
                {{-- Timeline 2 Center dot --}}
                <div class="col-sm-1 text-center flex-column d-none d-sm-flex">
                    <div class="row h-50">
                        <div class="col border-end">&nbsp;</div>
                        <div class="col">&nbsp;</div>
                    </div>
                    <h5 class="m-2">
                        <span class="badge rounded-pill bg-danger">&nbsp;</span>
                    </h5>
                    <div class="row h-50">
                        <div class="col border-end">&nbsp;</div>
                        <div class="col">&nbsp;</div>
                    </div>
                </div>
                <div class="col-sm"> <!--spacer--> </div>
            </div>
        </div>
    </section>

    {{-- Call to Action --}}
    @include('homepage.cta-section')

    {{-- news section --}}
    @include('homepage.news-section')

    {{-- FAQ --}}
    @include('homepage.faq-section')

@endsection
