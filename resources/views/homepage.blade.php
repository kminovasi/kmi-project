@extends('layouts.guest')
@section('title', 'home')

@section('content')

    {{-- Hero Section --}}
    @include('homepage.hero-section')

    {{-- Info Section --}}
    {{-- @include('homepage.info-section') --}}

    {{-- news section --}}
    @include('homepage.news-section')

    {{-- Content Section --}}
    @include('homepage.content-section')

    {{-- Timeline --}}
    @include('homepage.timeline-section')

    {{-- Call to Action --}}
    @include('homepage.cta-section')

    {{-- FAQ --}}
    @include('homepage.faq-section')

@endsection
