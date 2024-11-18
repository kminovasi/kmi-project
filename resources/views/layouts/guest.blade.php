<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/sigialogo.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>@yield('title')</title>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary position-fixed w-100 top-0 start-0" style="z-index: 9;">
        <div class="container-fluid mx-5">
            <a class="navbar-brand" href="#">
                <div class="d-flex align-items-center text-color-main">
                    <img class="me-3" src="{{ asset('assets/landingpage/logo.png') }}" alt="">
                    <div>
                        Portal <b>Inovasi</b>
                    </div>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active text-color-main" aria-current="page" href="#hero">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-color-main" href="#pendaftaran">Event</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link text-color-main" href="#info">InovasiTV</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link text-color-main" href="#footer">Kontak</a>
                    </li>
                    @if (Auth::user())
                        <li class="nav-item">
                            <a class="nav-link text-color-main" href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            @method('post')
                            <button type="submit" class="btn btn-danger">Keluar</button>
                        </form>
                    @else
                        <button class="btn btn-sm btn-danger">
                            <a class="nav-link text-white" href="{{ route('login') }}">Masuk</a>
                        </button>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <main class="container">
    @yield('content')
    </main>

    <footer style="background-color: #a00000">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6 p-md-4">
                    <a href="#" class="footer-site-logo d-block mb-4 text-decoration-none text-white"><h1>Innovation Award</h1></a>
                    <p class="text-white">Lorem ipsum dolor sit amet consectetur adipisicing elit. Eligendi quasi perferendis ratione perspiciatis accusantium.</p>
                </div>
                <div class="col-md d-flex justify-content-md-end p-2">
                    <ul class="list-unstyled nav-links">
                        <li><a class="text-decoration-none text-white" href="#">Home</a></li>
                        <li><a class="text-decoration-none text-white" href="#">About Us</a></li>
                        <li><a class="text-decoration-none text-white" href="#">Portfolio</a></li>
                        <li><a class="text-decoration-none text-white" href="#">Services</a></li>
                      </ul>
                </div>
                <div class="col-md d-flex justify-content-md-end p-2">
                    <ul class="list-unstyled nav-links">
                        <li><a class="text-decoration-none text-white" href="#">Home</a></li>
                        <li><a class="text-decoration-none text-white" href="#">About Us</a></li>
                        <li><a class="text-decoration-none text-white" href="#">Portfolio</a></li>
                        <li><a class="text-decoration-none text-white" href="#">Services</a></li>
                      </ul>
                </div>
            </div>
            <hr class="text-white">
            <div class="row">
                <div class="col-12 text-center text-white mt-3">
                    <p><small>© 2019-2020 All Rights Reserved.</small></p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
