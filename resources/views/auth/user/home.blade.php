@extends('layouts.app')
@section('title', 'Dashboard')
@section('css')
@endsection

@section('content')
    <!-- Your content for the home page here -->
    <div class="bgBase1">
        <x-dashboard.header/>

        <!-- Main page content-->
        <div class="container-xl px-4 ">
            <div class="row">
                <div class="col-md-7 col-sm-6">
                    <div class="row mb-3">
                    </div>
                <x-dashboard.card
                    :breakthrough-innovation="$breakthroughInnovation"
                    :detail-breakthrough-innovation-management="$detailBreakthroughInnovationManagement"
                    :incremental-innovation="$incrementalInnovation"
                    :detail-incremental-innovation-g-k-m-office="$detailIncrementalInnovationGKMOffice"
                    :detail-incremental-innovation-p-k-m-office="$detailIncrementalInnovationPKMOffice"
                    :detail-incremental-innovation-s-s-plant="$detailIncrementalInnovationSSPlant"
                    :idea-box="$ideaBox"
                    :detail-idea-box-idea="$detailIdeaBoxIdea"
                    :detail-breakthrough-innovation-p-b-b="$detailBreakthroughInnovationPBB"
                    :detail-breakthrough-innovation-t-p-p="$detailBreakthroughInnovationTPP"
                    :detail-incremental-innovation-p-k-m-plant="$detailIncrementalInnovationPKMPlant"
                    />

                    <div class="row">
                    </div>
                    <x-dashboard.semen :year="$year" />
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <h6>Productivity</h6>
                    </div>
                    <div class="row">

                    </div>
                </div>
            </div>
            <!-- Example Colored Cards for Dashboard Demo-->


        </div>
    </div>
@endsection
