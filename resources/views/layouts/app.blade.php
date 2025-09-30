<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>@yield('title') - SIG Innovation</title>

    @laravelPWA
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    {{-- Vite handles CSS & JS --}}
    <link rel="stylesheet" href="{{ asset('build/assets/app-6037ed8c.css') }}">
    <script src="{{ asset('build/assets/app-e4120a98.js') }}" type="module"></script>

    @livewireStyles

    {{-- Library tambahan --}}
    <link href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" rel="stylesheet" />
    <link href="{{ asset('template/dist/css/styles.css') }}" rel="stylesheet" />
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/icons/favicon2.png') }}" />
    <script data-search-pseudo-elements defer
        src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="{{ asset('summernote/summernote-lite.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @stack('css')
</head>

<body class="nav-fixed">
    @include('layouts.navbar')

    <div id="layoutSidenav">
        @include('layouts.sidebar')

        <div id="layoutSidenav_content">
            <main>
                @yield('content')
            </main>
            <footer class="footer-admin mt-auto footer-light text-black">
                <div class="container-xl px-4">
                    <div class="row">
                        <div class="col-md-6 small">Copyright &copy; KMI Website 2025</div>
                        <div class="col-md-6 text-md-end small">
                            <a href="#!">Privacy Policy</a>
                            &middot;
                            <a href="#!">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
    </script>

    @livewireScripts
    <script src="{{ asset('template/dist/js/scripts.js') }}"></script>
    <script src="{{ asset('template/dist/js/litepicker.js') }}"></script>
    <script src="{{ asset('summernote/summernote-lite.js') }}"></script>

    @stack('js')
</body>

</html>
