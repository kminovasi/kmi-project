@extends('layouts.app')
@section('title', 'Create Template')
@section('content')
    <!-- Your content for the home page here -->
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row d-flex align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3 ">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="edit-3"></i></div>
                            Template Assessment
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{route('assessment.show.template')}}">
                            <i class="me-1" data-feather="chevron-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <!-- Account page navigation-->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-10 col-md-11 col-sm-11">
                <!-- Account details card-->
                <div class="card mb-4">
                    <div class="card-header">Form Template Assessment</div>
                    <div class="card-body">
                        <form action="{{ route('assessment.store.template') }}" method="POST">
                            @csrf
                            <!-- Form Group (Point) -->
                            <div class="mb-3">
                                <label class="form-label" for="dataPoint">Point Assessment</label>
                                <input class="form-control" id="dataPoint" type="text" placeholder="Enter point assessment" name="point" value="" />
                            </div>

                            <!-- Form Group (Detail) -->
                            <div class="mb-3">
                                <label class="form-label" for="dataDetail">Detail</label>
                                <textarea name="detail_point" id="dataDetail" cols="10" rows="5" class="form-control"></textarea>
                            </div>

                            <!-- Form Row -->
                            <div class="row g-3 mb-3">
                                <!-- Form Group (Cluster) -->
                                <div class="col-md-3">
                                    <label class="form-label" for="dataCategory">Cluster</label>
                                    <select name="category" id="dataCategory" class="form-select" onchange="if_idea_or_biorii()">
                                        <option value="BI/II">Implemented</option>
                                        <option value="IDEA">IDEA Box</option>
                                    </select>
                                </div>

                                <!-- Form Group (Maximum Score) -->
                                <div class="col-md-3">
                                    <label class="form-label" for="dataSkor">Maximum Score</label>
                                    <input name="score_max" class="form-control" id="dataSkor" type="number" placeholder="Enter maximum score" value="" />
                                </div>

                                <!-- Form Group (Stage) -->
                                <div class="col-md-3">
                                    <label class="form-label" for="dataStage">Stage</label>
                                    <select name="stage" id="dataStage" class="form-select">
                                        <option value="on desk">On Desk</option>
                                        <option value="presentation">Presentation</option>
                                    </select>
                                </div>

                                <!-- Form Group (PDCA) -->
                                <div class="col-md-3">
                                    <label class="form-label" for="dataPDCA">PDCA</label>
                                    <select name="pdca" id="dataPDCA" class="form-select">
                                        <option value="Plan">PLAN</option>
                                        <option value="Do">DO</option>
                                        <option value="Check">CHECK</option>
                                        <option value="Action">ACTION</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid">
                                <button class="btn btn-primary" type="submit" id="button_submit">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script type="">
    $(document).ready(function() {
        if_idea_or_biorii()
    })

    function if_idea_or_biorii(){
        category = document.getElementById('dataCategory').value;

        if(category == 'BI/II'){
            document.getElementById('dataPDCA').removeAttribute('disabled')
        }else{
            document.getElementById('dataPDCA').setAttribute('disabled', true)
        }
    }
</script>
@endpush
