@extends('layouts.guest')
@section('title', )

@section('content')

<section class="hero-section text-white text-center d-flex align-items-center" style="height: 60vh;">
    <div class="container text-black">
        <h1 class="display-4 fw-bold">{{ $post->title }}</h1>
        <p class="mb-2">Ditulis oleh <span class="fw-semibold">{{ $post->user->username }}</span></p>
        <small class="text-dark">{{ $post->created_at->diffForHumans() }}</small>
    </div>
</section>

<div class="container-md ">
    <figure class="text-center">
        <img class="img-fluid border rounded-3 shadow-lg mb-4 overflow-hidden" width="700" height="500" loading="lazy"
        src="{{ route('dashboard.getFile') }}?directory={{ urlencode($post->cover_image) }}" alt="content-image">
    </figure>
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
            } else {
                navbar.classList.remove("bg-white", "shadow-sm");
                navbar.classList.add("bg-transparent");
            }
        });
    });
</script>
@endpush
