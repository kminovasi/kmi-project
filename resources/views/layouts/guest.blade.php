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
    <script data-search-pseudo-elements defer
        src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('owl-carrousel/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('owl-carrousel/owl.theme.default.min.css') }}">
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

    <x-guest-navbar />

    <main>
        @yield('content')
    </main>

    <x-guest-footer />



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
    integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
    integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>
    <script src="{{ asset('owl-carrousel/jquery.min.js') }}"></script>
    <script src="{{ asset('owl-carrousel/owl.carousel.min.js') }}"></script>

    @stack('js')
</body>

</html>
