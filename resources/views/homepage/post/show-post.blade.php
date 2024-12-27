@extends('layouts.guest')
@section('title', '{{ $post->title }}')

@section('content')

<section class="hero-section text-white text-center d-flex align-items-center position-relative"
    style="background-image: url('{{ asset('storage/' . $post->cover_image) }}');
           background-size: cover;
           background-position: center;
           height: 60vh;
           filter: brightness(0.5);">
    <div class="overlay position-absolute w-100 h-100" style="background: rgba(0, 0, 0, 0.5);"></div>
    <div class="container position-relative">
        <div class="hero-content mt-5">
            <h1 class="display-4 fw-bold">{{ $post->title }}</h1>
            <p class="mb-2">Ditulis oleh <span class="fw-semibold">{{ $post->user->username }}</span></p>
            <small class="text-light">{{ $post->created_at->diffForHumans() }}</small>
        </div>
    </div>
</section>

<div class="container-md py-4">
    <div class="content mb-4 lead">
        {!! $post->content !!}
    </div>
</div>

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
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.style.color = '';
                });
            } else {
                navbar.classList.remove("bg-white", "shadow-sm");
                navbar.classList.add("bg-transparent");
                document.querySelectorAll('.nav-link').forEach(link => {
                    link.style.color = 'white';
                });
            }
        });
    });
</script>
@endpush
