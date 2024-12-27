@extends('layouts.guest')
@section('title', 'berita')

@section('content')

<section class="hero-section text-white text-center d-flex align-items-center" style="background-color: #a00000;
           background-size: cover;
           background-position: center;
           height: 50vh;">
    <div class="container">
        <div class="hero-content">
            <h1 class="display-4 fw-bold">Post</h1>
        </div>
    </div>
</section>

<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            @foreach ($posts as $post )
            <div class="p-2 border rounded-2">
                <img class="img-fluid rounded-2" src="{{ 'storage/' . $post->cover_image }}" alt="">
                <div class="p-2">
                    <a href="{{ route('post.show', $post->slug) }}" class="text-decoration-none text-dark"><h4>{{ $post->title }}</h4></a>
                    <small class="text-muted mb-1"><strong>{{ $post->user->username }}</strong></small>
                    <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
                </div>
            </div>
            @endforeach
            @if ($posts->hasPages())
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
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
