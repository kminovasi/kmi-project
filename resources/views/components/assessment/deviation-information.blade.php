<div class="row m-auto mt-3">
    @if ($judgeCount == 1)
        <div class="col-7 text-center mx-auto">
            <h6 class="fw-bold">Total Nilai Juri</h6>
            @foreach($assignmentJudgeData as $judgeData)
                <div class="mb-3">
                    <input type="number" class="form-control text-center w-50 mx-auto fw-bold" id="judgeScore{{ $judgeData->judge_id }}" value="{{ $judgeData->total_score }}" readonly>
                </div>
            @endforeach
        </div>
    @else
        <div class="col-7">
            <h6>Total Nilai Tiap Juri</h6>
            @foreach($assignmentJudgeData as $judgeData)
                <div class="mb-3 row">
                    <label for="judgeScore{{ $judgeData->judge_id }}" class="col-sm-4 col-form-label">{{ $judgeData->judge_name }}</label>
                    <div class="col-sm-8">
                        <input type="number" class="form-control text-center" id="judgeScore{{ $judgeData->judge_id }}" value="{{ $judgeData->total_score }}" readonly>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-5 ">
            <h6>Informasi Deviasi: Total Poin Terbesar dan Terkecil</h6>
            <div class="row d-flex align-items-center">
                <div class="col-6">
                    <p>Poin Deviasi</p>
                    <p class="fs-1 fw-bold">{{ $deviantPoint }}</p>
                </div>
                <div class="col-6">
                    <p>Persentase Deviasi (Max: 10%)</p>
                    @if($deviantPercentage > 10)
                        <p class="fs-1 fw-bold text-danger">{{ $deviantPercentage }}%</p>
                    @else
                        <p class="fs-1 fw-bold text-success">{{ $deviantPercentage }}%</p>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>