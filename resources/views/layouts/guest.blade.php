<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/sigialogo.png') }}" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <title>@yield('title')</title>
</head>

<style>
    .accordion-button {
        /* Bootstrap danger color */
        background-color: #fff;
        /* Light danger */
        border-color: #dc3545;
    }

    .accordion-button:not(.collapsed) {
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

    .navbar-nav .nav-link.active {
        color: red;
    }

    .nav-link {
        position: relative;
    }

    .nav-link::after {
        content: '';
        display: block;
        width: 100%;
        height: 2px;
        background-color: red;
        position: absolute;
        left: 0;
        bottom: -5px;
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .nav-link:hover::after {
        transform: scaleX(1);
    }
</style>

<body>
    <nav class="navbar navbar-expand-lg bg-light fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="d-flex align-items-center text-color-main">
                    <img class="me-3" src="{{ asset('assets/landingpage/logo.png') }}" alt="">
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvasLg"
                aria-controls="navbarOffcanvasLg">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="navbarOffcanvasLg"
                aria-labelledby="navbarOffcanvasLgLabel">

                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Offcanvas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Link</a>
                        </li>
                        @if (Auth::user())
                            <li class="nav-item">
                                <a class="nav-link text-color-main" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="post">
                                    @csrf
                                    @method('post')
                                    <button type="submit" class="btn btn-danger">Keluar</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <button class="btn btn-sm btn-danger">
                                    <a class="nav-link text-white" href="{{ route('login') }}">Masuk</a>
                                </button>
                            </li>
                        @endif
                    </ul>
                </div>

            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer style="background-color: #a00000">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-6 p-md-4">
                    <a href="#" class="footer-site-logo d-block mb-4 text-decoration-none text-white">
                        <img src="{{ asset('assets/landingpage/logo.png') }}" alt="">
                    </a>
                    <p class="text-white">Lorem ipsum dolor sit amet consectetur adipisicing elit. Eligendi quasi
                        perferendis ratione perspiciatis accusantium.</p>
                </div>
                <div class="col-md d-flex justify-content-md-end p-3">
                    <ul class="list-unstyled nav-links">
                        <li><a class="text-decoration-none text-white" href="#">Home</a></li>
                        <li><a class="text-decoration-none text-white" href="#">About Us</a></li>
                        <li><a class="text-decoration-none text-white" href="#">Portfolio</a></li>
                        <li><a class="text-decoration-none text-white" href="#">Services</a></li>
                    </ul>
                </div>
                <div class="col-md d-flex justify-content-md-end p-3">
                    <ul class="list-unstyled nav-links">
                        <a href="https://facebook.com" class="text-white me-2">Email<i class="bi bi-facebook"></i></a>
                        <a href="https://twitter.com" class="text-white me-2"><i class="bi bi-twitter"></i></a>
                        <a href="https://instagram.com" class="text-white me-2"><i class="bi bi-instagram"></i></a>
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>

</body>

</html>
