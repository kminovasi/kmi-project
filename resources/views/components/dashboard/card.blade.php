<div class="row mb-3">
    @vite(['resources/css/dashboard.css'])
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-primary text-white h-100 ">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">BREAKTHROUGH INNOVATION</div>
                        <div class="text-lg fw-bold">
                            {{ $breakthroughInnovation }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- modal --}}
            <div class="modal fade" id="breakthroughInnovationModal">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Detail: Breakthrough Innovation</h2>
                        </div>
                        <div class="modal-body text-black">
                            Produk Dan Bahan Baku : {{ $detailBreakthroughInnovationPBB }} <br>
                            Teknologi Dan Proses Produksi : {{ $detailBreakthroughInnovationTPP }} <br>
                            Manajemen : {{ $detailBreakthroughInnovationManagement }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- end-modal --}}
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="#" data-bs-toggle="modal"
                    data-bs-target="#breakthroughInnovationModal">View Details</a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">INCREMENTAL INNOVATION</div>
                        <div class="text-lg fw-bold">
                            {{ $incrementalInnovation }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- modal --}}
            <div class="modal fade" id="incrementalInnovationModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Detail: Incremental Innovation</h2>
                        </div>
                        <div class="modal-body text-black">

                            GKM Plant : {{ $detailIncrementalInnovationGKMPlant }} <br>
                            GKM Office : {{ $detailIncrementalInnovationGKMOffice }} <br>
                            PKM Plant : {{ $detailIncrementalInnovationPKMPlant }} <br>
                            PKM Office : {{ $detailIncrementalInnovationPKMOffice }} <br>
                            SS Plant : {{ $detailIncrementalInnovationSSPlant }} <br>
                            SS Office : {{ $detailIncrementalInnovationSSOffice }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- end-modal --}}
            <div class="card-footer d-flex align-items-center justify-content-between small">

                <a class="text-white stretched-link" href="#" data-bs-toggle="modal"
                    data-bs-target="#incrementalInnovationModal">View Details</a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">IDEABOX</div>
                        <div class="text-lg fw-bold">
                            {{ $ideaBox }}
                        </div>
                    </div>
                </div>
            </div>
            {{-- modal --}}
            <div class="modal fade" id="ideaBoxModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 class="modal-title">Detail: Idea Box</h2>
                        </div>
                        <div class="modal-body text-black">
                            Idea Box : {{ $detailIdeaBoxIdea }} <br>
                        </div>
                    </div>
                </div>
            </div>
            {{-- end-modal --}}
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link" href="#" data-bs-toggle="modal"
                    data-bs-target="#ideaBoxModal">View Details</a>
                <div class="text-white"><i class="fas fa-angle-right"></i></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-teal text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Innovator</div>
                        <div class="text-lg fw-bold">
                            {{ $totalInnovators }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-blue text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Innovator Pria</div>
                        <div class="text-lg fw-bold">
                            {{ $totalInnovatorsMale }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-xl-4 mb-4">
        <div class="card bg-pink text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="me-3">
                        <div class="text-white-75 small">Total Innovator Wanita</div>
                        <div class="text-lg fw-bold">
                            {{ $totalInnovatorsFemale }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
