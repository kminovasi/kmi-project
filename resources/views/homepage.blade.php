@extends('layouts.guest')
@section('title', 'home')

@section('content')

    <section class="relative pt-5 pb-5 bg-dark bg-cover mt-5"
        style="background-image:url(https://images.unsplash.com/photo-1549082984-1323b94df9a6?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80)">
        <!-- Content -->
        <div class="container max-w-screen-xl position-relative overlap-10 text-center text-lg-start pt-5 pb-5 pt-lg-6">
            <div class="row row-grid align-items-center">
                <div class="col-lg-8 text-center text-lg-start">
                    <!-- Title -->
                    <h1 class="fw-bold font-bolder display-5 text-dark mb-5">
                        Portal Inovasi
                    </h1>
                    <!-- Text -->
                    <p class="lead text-dark text-opacity-75 mb-10 w-lg-2/3">
                        Lorem ipsum dolor sit amet consectetur adipisicing elit. Esse quibusdam cupiditate quasi eligendi explicabo minus voluptatem natus odit impedit cum.
                    </p>
                    <!-- Buttons -->
                    <div class="mt-10 mx-n2">
                        <a href="#" class="btn btn-lg btn-primary shadow-sm mx-2 px-lg-8">
                            Dashboard
                        </a>
                        <a href="#" class="btn btn-lg btn-neutral mx-2 px-lg-8">
                            Learn more
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
    <section class="container py-5">
        <div class="shadow rounded text-center text-white p-5"
            style="  background: rgb(195, 34, 34);
            background: linear-gradient(
                120deg,
                rgba(195, 34, 34, 1) 25%,
                rgba(255, 143, 77, 1) 100%
            );">
            <h2 class="display-5 fw-bold text-white mt-3">
                Jadilah bagian dari inovasi besar
            </h2>
            <p class="lead text-white mt-3">
                Bergabunglah sekarang dan jadilah inovator terbaik tahun ini.
            </p>
            <a href="/dashboard" class="btn btn-warning text-black text-decoration-none mt-3 mb-3 shadow">Daftarkan Tim
                Anda</a>
        </div>
    </section>

    {{-- Questions and Answer --}}
    <section class="container py-5">
        <header class="text-center">
            <h2 class="display-5 fw-bold text-danger mt-3">Q&A</h2>
            <p class="lead text-muted mt-3">
                Pertanyaan yang sering diajukan.
            </p>
        </header>
        <div>
            <div class="accordion accordion-flush" id="accordion">
                <div class="accordion-item">
                    <h2 class="accordion-header lead" id="flush-heading1">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#item-1" aria-expanded="false" aria-controls="item-1">
                            Bagaimana cara mendaftar untuk acara?
                        </button>
                    </h2>
                    <div id="item-1" class="accordion-collapse collapse" aria-labelledby="flush-heading1"
                        data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <p class="text-muted">
                                Anda dapat mendaftar melalui situs web kami. Cukup pilih acara yang ingin Anda ikuti dan
                                ikuti petunjuk pendaftaran yang tersedia.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header fw-bold lead" id="flush-heading2">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#item-2" aria-expanded="false" aria-controls="item-2">
                            Di mana lokasi acara diadakan?
                        </button>
                    </h2>
                    <div id="item-2" class="accordion-collapse collapse" aria-labelledby="flush-heading2"
                        data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <p class="text-muted">

                            </p>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header fw-bold" id="flush-heading3">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#item-3" aria-expanded="false" aria-controls="item-3">
                            Bagaimana jika saya memiliki pertanyaan lebih lanjut?
                        </button>
                    </h2>
                    <div id="item-3" class="accordion-collapse collapse" aria-labelledby="flush-heading3"
                        data-bs-parent="#accordion">
                        <div class="accordion-body">
                            <p class="text-muted">
                                Anda dapat menghubungi tim kami melalui informasi kontak di bawah ini, dan kami akan dengan
                                senang hati membantu Anda.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
