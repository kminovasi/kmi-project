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

@push('js')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const navbar = document.getElementById("guest-navbar");
        const heroSection = document.querySelector(".hero-section");

        window.addEventListener("scroll", function () {
            const heroHeight = heroSection.offsetHeight;
            if (window.scrollY > heroHeight) {
                navbar.classList.remove("bg-transparent");
                navbar.classList.add("bg-white", "shadow-sm");
            } else {
                navbar.classList.remove("bg-white", "shadow-sm");
                navbar.classList.add("bg-transparent");
            }
        });
    });
</script>
@endpush
