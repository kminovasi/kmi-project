@extends('layouts.guest')
@section('title', '{{ $post->title }}')

@section('content')

<section class="hero-section text-white text-center d-flex align-items-center"
    style="background-image: url('{{ asset('storage/' . $post->cover_image) }}');
           background-size: cover;
           background-position: center;
           height: 60vh;">
    <div class="container">
        <div class="hero-content">
            <h1 class="display-4 fw-bold">{{ $post->title }}</h1>
            <p class="mb-2">Ditulis oleh <span class="fw-semibold">{{ $post->user->username }}</span></p>
            <small class="text-light">{{ $post->created_at->diffForHumans() }}</small>
        </div>
    </div>
</section>


<div class="container-md py-4">
    <header class="text-center mb-4">
        <h1 class="fw-bold">{{ $post->title }}</h1>
        <p class="text-muted mb-1">Ditulis oleh {{ $post->user->username }}</p>
        <small class="text-muted">{{ $post->created_at->diffForHumans() }}</small>
    </header>
    <figure class="text-center mb-4">
        <img src="{{ asset('storage/' . $post->cover_image) }}" alt="cover image"
            class="img-fluid rounded shadow w-100" style="max-width: 100%; height: auto;">
    </figure>
    <div class="content mb-4 lead">
        {!! $post->content !!}
    </div>
</div>

@endsection
