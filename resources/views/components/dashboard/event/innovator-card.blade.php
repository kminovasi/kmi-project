<div class="dashboard-card">
    <div class="card-header text-center">
        <h5 class="fw-bold text-primary">Statistik Inovator</h5>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-12 col-md-4 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-rocket fa-3x text-info"></i>
                        <span class="badge bg-info text-white rounded-pill">Inovasi</span>
                    </div>
                    <h5 class="fw-bold mt-2">{{ $statistics['totalInnovation'] }}</h5>
                    <p class="text-muted">Total Inovasi</p>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-lightbulb fa-3x text-warning"></i>
                        <span class="badge bg-warning text-dark rounded-pill">Idea Box</span>
                    </div>
                    <h5 class="fw-bold mt-2">{{ $statistics['ideaBox'] }}</h5>
                    <p class="text-muted">Jumlah Idea Box</p>
                </div>
            </div>
            <div class="col-12 col-md-4 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fa-solid fa-users fa-3x text-success"></i>
                        <span class="badge bg-success text-white rounded-pill">Total Inovator</span>
                    </div>
                    <h5 class="fw-bold mt-2">{{ $statistics['totalInnovators'] }}</h5>
                    <p class="text-muted">Total Inovator</p>
                </div>
            </div>
        </div>

        <hr>

        <div class="row text-center">
            <div class="col-12 col-md-6 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fas fa-male fa-3x text-primary"></i>
                        <span class="badge bg-primary text-white rounded-pill">Laki-Laki</span>
                    </div>
                    <h5 class="fw-bold mt-2">{{ $statistics['totalInnovatorsMale'] }}</h5>
                    <p class="text-muted">Total Inovator Laki-Laki</p>
                </div>
            </div>
            <div class="col-12 col-md-6 mb-3">
                <div class="card p-3 shadow-sm border-0 rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <i class="fas fa-female fa-3x text-danger"></i>
                        <span class="badge bg-danger text-white rounded-pill">Perempuan</span>
                    </div>
                    <h5 class="fw-bold mt-2">{{ $statistics['totalInnovatorsFemale'] }}</h5>
                    <p class="text-muted">Total Inovator Perempuan</p>
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

    .statistic-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }

    .statistic-icon {
        font-size: 24px;
        color: #eb4a3a;
        margin-right: 15px;
    }

    .statistic-info {
        flex-grow: 1;
    }

    .statistic-title {
        font-weight: bold;
        margin: 0;
    }

    .statistic-value {
        margin: 0;
        color: #555;
    }

    hr {
        margin: 20px 0;
    }
</style>
