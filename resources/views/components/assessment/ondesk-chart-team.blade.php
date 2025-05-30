<div class="col-lg-5 col-xl-5 mb-8 mx-auto">
        <div class="bg-gradient-green text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Informasi Teks -->
                    <div class="me-3 flex-grow-1 d-flex flex-column gap-y-2">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">
                            <h5>
                                Total Team Terdaftar
                            </h5>
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center text-black">
                            {{ $totalTeams }}
                            <small class="ms-2">(Tim)</small>
                        </div>
                        <!-- Persentase laki-laki dan perempuan -->
                        <div class="mt-3 text-start">
                            <span style="font-weight: 600;">Total Team:</span>
                            <div class="mt-3 d-flex justify-content-between text-black">
                                <span>Lolos:</span>
                                <span class="fw-bold text-black">
                                    {{ $totalTeamsPassToOnDesk }} Tim
                                </span>
                            </div>
                            <div class="d-flex justify-content-between text-black">
                                <span>Tidak Lolos:</span>
                                <span class="fw-bold text-black">
                                    {{ $totalTeamsNotPassToOnDesk }} Tim
                                </span>
                            </div>
                        </div>

                    </div>
                    <!-- Chart -->
                    <div class="chart-container" style="width: 230px; height: 230px; background-color: transparent;">
                        <canvas id="onDeskChart" style="background-color: transparent;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('onDeskChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [
                        {{ $totalTeams > 0 ? round(($totalTeamsPassToOnDesk / $totalTeams) * 100, 2) : 0 }},
                        {{ $totalTeams > 0 ? round(($totalTeamsNotPassToOnDesk / $totalTeams) * 100, 2) : 0 }}
                    ],
                    backgroundColor: ['#000', '#c0c0c0'],
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            color: 'red',
                            font: {
                                size: 15
                            }
                        }
                    },
                    datalabels: {
                        color: 'red',
                        font: {
                            size: 20,
                            weight: 'bold'
                        },
                        formatter: (value) => `${value}%`, // Menampilkan persentase di dalam chart
                        anchor: 'bottom',
                        align: 'bottom'
                    },
                    tooltip: {
                        enabled: false
                        // Nonaktifkan tooltip jika tidak dibutuhkan
                    }
                },
                maintainAspectRatio: false,
                responsive: true
            },
            plugins: [ChartDataLabels]
        });
    });
</script>