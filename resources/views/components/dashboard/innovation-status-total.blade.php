<!-- resources/views/components/dashboard/innovation-status-total.blade.php -->
<div class="row mb-3">
    <!-- Not Implemented Card -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-danger h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 text-danger">Not Implemented Makalah</h6>
                        <h2 class="card-title mb-0">{{ $totals['Not Implemented'] }}</h2>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-x-circle text-danger fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" role="progressbar"
                            style="width: {{ $percentages['Not Implemented'] }}%"
                            aria-valuenow="{{ $percentages['Not Implemented'] }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">{{ $percentages['Not Implemented'] }}% dari total
                        inovasi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Implemented Card -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-success h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 text-success">Implemented Makalah</h6>
                        <h2 class="card-title mb-0">{{ $totals['Implemented'] }}</h2>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar"
                            style="width: {{ $percentages['Implemented'] }}%"
                            aria-valuenow="{{ $percentages['Implemented'] }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">{{ $percentages['Implemented'] }}% dari total inovasi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Card -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card border-warning h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-subtitle mb-2 text-warning">Progress Makalah</h6>
                        <h2 class="card-title mb-0">{{ $totals['Progress'] }}</h2>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-clock text-warning fs-4"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-warning" role="progressbar"
                            style="width: {{ $percentages['Progress'] }}%"
                            aria-valuenow="{{ $percentages['Progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <small class="text-muted mt-2 d-block">{{ $percentages['Progress'] }}% dari total inovasi</small>
                </div>
            </div>
        </div>
    </div>
</div>
