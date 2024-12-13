@extends('layouts.app')

@section('title', 'Statistik Event')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-12">
                <h2>Statistik untuk Event: {{ $eventName }}</h2>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-7 col-md-7 col-10 col-sm-12">
                <x-dashboard.event.innovator-card :event-id="$eventId" />
            </div>
        </div>
    </div>
@endsection
