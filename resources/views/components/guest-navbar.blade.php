<nav id="guest-navbar" class="navbar navbar-expand-lg bg-transparent fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <div class="d-flex align-items-center text-color-main">
                <img class="me-3" src="{{ asset('assets/landingpage/logo-rev2.png') }}" width="160px" alt="logo">
            </div>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarOffcanvasLg"
            aria-controls="navbarOffcanvasLg">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-end" tabindex="-1" id="navbarOffcanvasLg"
            aria-labelledby="navbarOffcanvasLgLabel">

            <div class="offcanvas-header">
                <a class="navbar-brand" href="#">
                    <div class="d-flex align-items-center text-color-main">
                        <img class="me-3" src="{{ asset('assets/landingpage/logo-rev2.png') }}" alt="logo">
                    </div>
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3 gap-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('homepage') ? 'active' : '' }}" href="{{ route('homepage') }}">Home</a>
                    </li>
                    <li class="nav-item mb-3">
                        <a class="nav-link {{ request()->routeIs('post.list') ? 'active' : '' }}" href="{{ route('post.list') }}">Berita</a>
                    </li>
                    @if (Auth::user())
                        @if (Auth::user()->role == 'admin' || Auth::user()->role == 'Superadmin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            @else
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('homepage') ? 'active' : '' }}" href="{{ route('dashboard') }}">Portal Inovasi SIG</a>
                        @endif
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                @method('post')
                                <button type="submit" class="btn btn-danger">Keluar</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class=" text-white btn btn-danger" href="{{ route('login') }}">Masuk</a>
                        </li>
                    @endif
                </ul>
            </div>

        </div>
    </div>
</nav>
