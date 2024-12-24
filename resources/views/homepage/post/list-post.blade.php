@extends('layouts.guest')
@section('title', '{{ $post->title }}')

@section('content')

<section class="text-white text-center d-flex align-items-center" style="background-color: #a00000;
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
