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
