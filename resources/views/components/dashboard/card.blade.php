<div class="row mb-3">
    @vite(['resources/css/dashboard.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    @push('css')
        <style>
            .bg-blue {
                background: linear-gradient(45deg, #4e73df, #224abe);
            }

            .bg-pink {
                background: linear-gradient(45deg, #e83e8c, #ba2465);
            }

            .bg-gradient-warning {
                background: linear-gradient(45deg, #ffc107, #ff9800);
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
                background: linear-gradient(45deg, #28a745, #218838);
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
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-gradient-primary text-white h-100 shadow-lg">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small mb-1">Total Inovasi</div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $breakthroughInnovation + $incrementalInnovation }}
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-rocket fa-xl text-white"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="#" data-bs-toggle="modal"
                    data-bs-target="#breakthroughInnovationModal">
                    Lihat Detail
                </a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="breakthroughInnovationModal" tabindex="-1"
        aria-labelledby="breakthroughInnovationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white" id="breakthroughInnovationModalLabel">
                        <i class="fa-solid fa-rocket me-2"></i>Detail Inovasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Breakthrough Innovation</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Produk Dan Bahan Baku
                                    <span
                                        class="badge bg-primary rounded-pill">{{ $detailBreakthroughInnovationPBB }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Teknologi Dan Proses Produksi
                                    <span
                                        class="badge bg-primary rounded-pill">{{ $detailBreakthroughInnovationTPP }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Manajemen
                                    <span
                                        class="badge bg-primary rounded-pill">{{ $detailBreakthroughInnovationManagement }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Incremental Innovation</h6>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    GKM Plant
                                    <span
                                        class="badge bg-success rounded-pill">{{ $detailIncrementalInnovationGKMPlant }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    GKM Office
                                    <span
                                        class="badge bg-success rounded-pill">{{ $detailIncrementalInnovationGKMOffice }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    PKM Plant
                                    <span
                                        class="badge bg-success rounded-pill">{{ $detailIncrementalInnovationPKMPlant }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    PKM Office
                                    <span
                                        class="badge bg-success rounded-pill">{{ $detailIncrementalInnovationPKMOffice }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    SS Plant
                                    <span
                                        class="badge bg-success rounded-pill">{{ $detailIncrementalInnovationSSPlant }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    SS Office
                                    <span
                                        class="badge bg-success rounded-pill">{{ $detailIncrementalInnovationSSOffice }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-gradient-warning text-white h-100 shadow-lg">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="text-white-75 small mb-1">Total IDE</div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $ideaBox }}
                            <small class="ms-2">(Ide)</small>
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-lightbulb fa-xl text-white"></i>
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

    <!-- Modal -->
    <div class="modal fade" id="ideaBoxModal" tabindex="-1" aria-labelledby="ideaBoxModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title" id="ideaBoxModalLabel">
                        <i class="fa-solid fa-lightbulb me-2"></i>Detail Idea Box
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Idea Box
                                    <span class="badge bg-warning rounded-pill">{{ $detailIdeaBoxIdea }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-teal text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="text-white-75 small mb-2">
                            Total Innovator
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovators }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fas fa-people-group"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-purple text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="text-white-75 small mb-2">
                            Total Innovator Laki-laki
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovatorsMale }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-mars fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-pink text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="text-white-75 small mb-2">
                            Total Innovator Perempuan
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalInnovatorsFemale }}
                            <small class="ms-2">(Orang)</small>
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fa-solid fa-venus fa-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($isSuperadmin || $isAdmin)
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-gradient-green text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3 flex-grow-1">
                        <div class="text-white-75 small mb-2">
                            Total Event Aktif
                        </div>
                        <div class="text-lg fw-bold d-flex align-items-center">
                            {{ $totalActiveEvents }}
                            <small class="ms-2">(Event)</small>
                        </div>
                    </div>
                    <div class="icon-circle bg-white-25 flex-shrink-0">
                        <i class="fas fa-calendar-alt fa-xl text-white"></i>
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
</div>
