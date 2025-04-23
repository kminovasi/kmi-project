<div class="row mb-3">
    @vite(['resources/css/dashboard.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    @push('css')
        <style>
            .bg-event {
                background: #D84040;
            }

            .bg-innovations {
                background: #D84040;
            }

            .bg-purple {
                background: linear-gradient(45deg, #6f42c1, #5e35b1);
            }


            .icon-circle {
                min-height: 3.5rem;
                min-width: 3.5rem;
                height: 3.5rem;
                width: 3.5rem;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(255, 255, 255, 0.1);
            }

            .bg-white-25 {
                background-color: rgba(255, 255, 255, 0.25);
            }

            .card-footer {
                background-color: rgba(0, 0, 0, 0.1);
                border-top: 1px solid rgba(255, 255, 255, 0.15);
                padding: 0.75rem 1.25rem;
            }

            .text-lg {
                font-size: 1.5rem;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .icon-circle {
                    min-height: 3rem;
                    min-width: 3rem;
                    height: 3rem;
                    width: 3rem;
                }

                .text-lg {
                    font-size: 1.25rem;
                }
            }

            .bg-gradient-primary {
                background: linear-gradient(45deg, #4e73df, #224abe);
            }

            .bg-gradient-green {
                background: #D84040;
            }


            .bg-white-25 {
                background-color: rgba(255, 255, 255, 0.25);
            }


            .text-lg {
                font-size: 1.5rem;
            }

            .modal-header {
                border-bottom: none;
            }

            .modal-footer {
                border-top: none;
            }

            .list-group-item {
                background-color: transparent;
            }
        </style>
    @endpush

    {{-- Total Innovation --}}
    <div class="col-lg-4 col-xl-5 mb-4 mx-auto">
        <div class="card bg-innovations text-white h-100 shadow-lg">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;"
                            data-bs-toggle="modal" data-bs-target="#exampleModal">Total Inovasi Kategori Implemented</div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            <!-- Menampilkan total jumlah inovasi berdasarkan kategori -->
                            {{ $totalImplementedInnovations }}
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-rocket fa-xl text-white"
                            style="font-size: 30px; font-weight: bolder; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="#" data-bs-toggle="modal"
                    data-bs-target="#exampleModal">
                    Lihat Detail
                </a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Detail Innovation -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header  text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="exampleModalLabel">
                        <i data-feather="zap" class="me-2"></i> <span class="fw-bold">Detail Inovasi</span>
                    </h5>
                    <button type="button" class="btn-close" style="color: black;" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light">
                    <div class="row">
                        @foreach ($implemented as $item)
                            @php
                                $colors = [
                                    'text-success',
                                    'text-warning',
                                    'text-info',
                                    'text-primary',
                                    'text-secondary',
                                ];
                                $icons = ['zap', 'layers', 'box', 'shield', 'star'];
                                $color = $colors[$loop->index % count($colors)];
                                $icon = $icons[$loop->index % count($icons)];
                            @endphp
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm border-0 rounded">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <i data-feather="{{ $icon }}" class="me-2 {{ $color }}"></i>
                                        <h5 class="m-0 fw-bold {{ $color }}">{{ $item["category_name"] }}</h5>
                                        <span class="badge bg-primary rounded-pill fs-5 fw-bold">
                                            {{ $item["count"] ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer bg-gradient-light">
                    <button type="button" class="btn btn-outline-primary fw-bold" data-bs-dismiss="modal">
                        <i data-feather="x-circle" class="me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Idea Box --}}
    <div class="col-lg-6 col-xl-5 mb-4 mx-auto">
        <div class="card bg-innovations text-white h-100 shadow-lg">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">Total Inovasi
                            Kategori IDEA BOX
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalIdeaBoxInnovations }}
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-lightbulb fa-xl text-white"
                            style="font-size: 30px; font-weight: bolder; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="#" data-bs-toggle="modal"
                    data-bs-target="#ideaBoxModal">
                    Lihat Detail
                </a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Detail Idea box -->
    <div class="modal fade" id="ideaBoxModal" tabindex="-1" aria-labelledby="ideaBoxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <div class="modal-header text-white">
                    <h5 class="modal-title fw-bold d-flex align-items-center" id="ideaBoxModalLabel">
                        <i class="fa-solid fa-lightbulb me-2"></i> <span class="fw-bold">Detail Idea Box</span>
                    </h5>
                    <button type="button" class="btn-close" style="color: black;" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light">
                    <div class="row">
                        @foreach ($ideaBox as $item)
                            @php
                                $colors = [
                                    'text-success',
                                    'text-warning',
                                    'text-info',
                                    'text-primary',
                                    'text-secondary',
                                ];
                                $icons = ['zap', 'layers', 'box', 'shield', 'star'];
                                $color = $colors[$loop->index % count($colors)];
                                $icon = $icons[$loop->index % count($icons)];
                            @endphp
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm border-0 rounded">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                        <i data-feather="{{ $icon }}" class="me-2 {{ $color }}"></i>
                                        <h5 class="m-0 fw-bold {{ $color }}">{{ $item["category_name"] }}</h5>
                                        <span class="badge bg-primary rounded-pill fs-5 fw-bold">
                                            {{ $item["count"] ?? 0 }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer bg-gradient-light">
                    <button type="button" class="btn btn-outline-primary fw-bold" data-bs-dismiss="modal">
                        <i class="fa-solid fa-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>



    {{-- Total Event Active --}}
    @if ($isSuperadmin || $isAdmin)
        <div class="col-lg-11 col-xl-11 mb-9 mx-auto">
            <div class="card bg-event text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3 flex-grow-1">
                            <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">Total
                                Event Aktif</div>
                            <div class="text-lg fw-bold d-flex align-items-center">
                                {{ $totalActiveEvents }}
                                <small class="ms-2">(Event)</small>
                            </div>
                        </div>
                        <div class="icon-circle bg-white-25 flex-shrink-0">
                            <i class="fas fa-calendar-alt fa-xl"
                                style="font-size: 40px; font-weight: bolder; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); color: #ffffff;"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link" href="{{ route('dashboard-event.list') }}">
                        Lihat Daftar Event
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    @endif

    <div class="col-12 mb-4"></div>

    <div class="col-lg-11 col-xl-11 mb-8 mx-auto">
        <div class="card bg-gradient-green text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Informasi Teks -->
                    <div class="me-3 flex-grow-1 d-flex flex-column gap-y-2">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">
                            Akumulasi Total Inovator
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovators }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                        <!-- Persentase laki-laki dan perempuan -->
                        <div class="mt-3">
                            <span style="font-weight: 600;">Total Inovator:</span>
                            <div class="mt-3 d-flex justify-content-between">
                                <span>Laki-laki:</span>
                                <span class="fw-bold">
                                    {{ $totalInnovatorsMale }} Orang
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Perempuan:</span>
                                <span class="fw-bold">
                                    {{ $totalInnovatorsFemale }} Orang
                                </span>
                            </div>
                        </div>

                    </div>
                    <!-- Chart -->
                    <div class="chart-container" style="width: 230px; height: 230px; background-color: transparent;">
                        <canvas id="innovatorChart" style="background-color: transparent;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-4"></div>

    <div class="col-lg-6 col-xl-5 mb-4 mx-auto">
        <div class="card bg-gradient-green text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">Total
                            Inovator Laki-laki
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovatorsMale }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-mars fa-xl text-blue"
                            style="font-size: 40px; font-weight: bolder; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); color: #ffffff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-xl-5 mb-4 mx-auto">
        <div class="card bg-gradient-green text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="small mb-1" style="font-weight: 700; font-size: 1rem; color: #ffffff;">Total
                            Inovator Perempuan</div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovatorsFemale }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-venus fa-xl text-pink"
                            style="font-size: 40px; font-weight: bolder; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); color: #ffffff;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('innovatorChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [
                        {{ $totalInnovators > 0 ? round(($totalInnovatorsMale / $totalInnovators) * 100, 2) : 0 }},
                        {{ $totalInnovators > 0 ? round(($totalInnovatorsFemale / $totalInnovators) * 100, 2) : 0 }}
                    ],
                    backgroundColor: ['#fff', '#c0c0c0'],
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
