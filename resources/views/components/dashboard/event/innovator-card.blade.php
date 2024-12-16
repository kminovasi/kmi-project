<div class="dashboard-card">
    <div class="card-header">
        <h5>Statistik Inovator</h5>
    </div>
    <div class="card-body">
        <div class="statistic-item">
            <div class="statistic-icon">
                <i class="fa-solid fa-rocket"></i>
            </div>
            <div class="statistic-info">
                <p class="statistic-title">Total Inovasi</p>
                <p class="statistic-value">{{ $statistics['totalInnovation'] }}</p>
            </div>
        </div>
        <div class="statistic-item">
            <div class="statistic-icon">
                <i class="fa-solid fa-lightbulb"></i>
            </div>
            <div class="statistic-info">
                <p class="statistic-title">Idea Box</p>
                <p class="statistic-value">{{ $statistics['ideaBox'] }}</p>
            </div>
        </div>
        <hr>
        <div class="statistic-item">
            <div class="statistic-icon">
                <i class="fas fa-male"></i>
            </div>
            <div class="statistic-info">
                <p class="statistic-title">Total Inovator Laki-Laki</p>
                <p class="statistic-value">{{ $statistics['totalInnovatorsMale'] }}</p>
            </div>
        </div>
        <div class="statistic-item">
            <div class="statistic-icon">
                <i class="fas fa-female"></i>
            </div>
            <div class="statistic-info">
                <p class="statistic-title">Total Inovator Perempuan</p>
                <p class="statistic-value">{{ $statistics['totalInnovatorsFemale'] }}</p>
            </div>
        </div>
        <div class="statistic-item">
            <div class="statistic-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="statistic-info">
                <p class="statistic-title">Total Inovator</p>
                <p class="statistic-value">{{ $statistics['totalInnovators'] }}</p>
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
