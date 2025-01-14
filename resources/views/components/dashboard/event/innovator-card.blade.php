<div class="dashboard-card">
    <div class="card-header text-center">
        <h5 class="fw-bold text fs-header" style="font-size: 1.2rem;">Statistik Inovator & Akumulasi Benefit</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-12 col-md-4 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-rocket fa-2x text-info"></i> <!-- Kurangi ukuran ikon -->
                        <span class="badge bg-info text-white rounded-pill">Inovasi</span>
                    </div>
                    <h5 class="fw-bold mt-2" style="font-size: 1.8rem;">{{ $statistics['totalInnovation'] }}</h5>
                    <p class="text-muted" style="font-size: 1rem;">Total Inovasi</p>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-lightbulb fa-2x text-warning"></i>
                        <span class="badge bg-warning text-dark rounded-pill">Idea Box</span>
                    </div>
                    <h5 class="fw-bold mt-2" style="font-size: 1.8rem;">{{ $statistics['ideaBox'] }}</h5>
                    <p class="text-muted" style="font-size: 1rem;">Jumlah Idea Box</p>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-users fa-2x text-success"></i>
                        <span class="badge bg-success text-white rounded-pill">Total Inovator</span>
                    </div>
                    <h5 class="fw-bold mt-2" style="font-size: 1.8rem;">{{ $statistics['totalInnovators'] }}</h5>
                    <p class="text-muted" style="font-size: 1rem;">Total Inovator</p>
                </div>
            </div>
        </div>

        <hr>

        <div class="row text-center">
            <div class="col-12 col-md-6 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fas fa-male fa-2x text-primary"></i>
                        <span class="badge bg-primary text-white rounded-pill">Laki-Laki</span>
                    </div>
                    <h5 class="fw-bold mt-2" style="font-size: 1.8rem;">{{ $statistics['totalInnovatorsMale'] }}</h5>
                    <p class="text-muted" style="font-size: 1rem;">Total Inovator Laki-Laki</p>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fas fa-female fa-2x text-danger"></i>
                        <span class="badge bg-danger text-white rounded-pill">Perempuan</span>
                    </div>
                    <h5 class="fw-bold mt-2" style="font-size: 1.8rem;">{{ $statistics['totalInnovatorsFemale'] }}</h5>
                    <p class="text-muted" style="font-size: 1rem;">Total Inovator Perempuan</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin: 20px;
        padding: 20px;
    }

    .card-header {
        border-bottom: 2px solid #eb4a3a;
        margin-bottom: 15px;
    }

    .card p {
        margin: 0;
    }

    hr {
        margin: 20px 0;
    }
</style>
