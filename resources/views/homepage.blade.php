@extends('layouts.guest')
@section('title', 'home')

@section('content')

    <section class="container-fluid justify-content-center  p-0" id="hero">
        <div class="padding-for-content-to-navbar   "></div>
        <div class="row align-items-start">
            <img id="hero-background" src="{{ asset('assets/landingpage/beranda-2.png') }}" alt="">
        </div>
    </section>


    <section>
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
    </section>

    {{-- Timeline --}}
    <section>
        <header class="text-center mb-4">
            <h1>Timeline Event</h1>
            <p class="text-muted">Catat tanggal-tanggal penting dan jangan lewatkan setiap momen berharga!</p>
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
                            <div class="float-end text-danger small">Jan 9th 2021 7:00 AM</div>
                            <h4 class="card-title text-danger">Day 1 Orientation</h4>
                            <p class="card-text">Welcome to the campus, introduction and get started with the tour.
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
                            <div class="float-end text-danger small">Jan 10th 2021 8:30 AM</div>
                            <h4 class="card-title text-danger">Day 2 Sessions</h4>
                            <p class="card-text">Sign-up for the lessons and speakers that coincide with your
                                course
                                syllabus. Meet and greet with instructors.</p>
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
    <section>
        <div class="shadow rounded text-center text-white p-5 my-5"
            style="  background: rgb(195, 34, 34);
            background: linear-gradient(
                120deg,
                rgba(195, 34, 34, 1) 25%,
                rgba(255, 143, 77, 1) 100%
            );">
            <h2>Jadilah bagian dari inovasi besar</h2>
            <p>Bergabunglah sekarang dan jadilah inocator terbaik tahun ini.</p>
            <a href="/dashboard" class="btn btn-warning text-black text-decoration-none">Login</a>
        </div>
    </section>

    {{-- Logo Cloud --}}
    <section>
        <div class="py-5">
            <header>
                <h1>Kontribusi SIG Group</h1>
            </header>
            <div class="row p-4">
                <div class="col-sm-3 col-md-4">
                    <figure>
                        <img src="" alt="smbr">
                    </figure>
                </div>
            </div>
        </div>
    </section>

@endsection
