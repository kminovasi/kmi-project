<section class="hero-section d-flex align-items-center bg-light " >
    <div class="container mt-5 py-5">
        <div class="row align-items-center">
            <!-- Left Content -->
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="display-4 fw-bold text-danger mb-3">
                    Mendorong <span class="text-dark">Inovasi</span>, Membangun Masa Depan
                </h1>
                <p class="lead text-muted mb-4">
                    SIG Innovation Award hadir untuk mengapresiasi solusi kreatif yang membawa perusahaan menuju keberlanjutan dan pertumbuhan.
                </p>
                <div class="mb-3 mb-lg-0">
                    <a href="{{ route('dashboard') }}" class="btn btn-danger btn-lg me-3">
                        Masuk Portal
                    </a>
                    <a href="{{ route('evidence.index') }}" class="btn btn-outline-danger btn-lg">
                        Lihat Inovasi
                    </a>
                </div>
            </div>
            <!-- Right Image -->
            <div class="col-lg-6 text-center">
                <img src="{{ asset('assets/hero.png') }}" alt="Innovation" class="img-fluid">
            </div>
        </div>
    </div>
</section>