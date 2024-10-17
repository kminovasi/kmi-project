@extends('layouts.app')
@section('title', 'Manajemen Notifikasi')
@section('content')
<div class="container">
    <h1 class="mt-3">Manajemen Notifikasi</h1>

    <!-- Tombol untuk menandai semua notifikasi sebagai telah dibaca -->
    <form action="{{ route('notifications.readAll') }}" method="POST" class="mb-4">
        @csrf
        <button type="submit" class="btn btn-primary">Tandai Semua Sebagai Dibaca</button>
    </form>

    <form action="{{ route('notifications.destroyAll') }}" method="POST" class="mb-4">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Hapus Semua Notifikasi</button>
    </form>


    @if($notifications->count() > 0)
        <ul class="list-group">
            @foreach($notifications as $notification)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <a href="{{ $notification->data['url'] }}" class="{{ $notification->read_at ? 'text-muted' : 'text-dark' }}">
                        {{ $notification->data['message'] }}
                    </a>
                    <br>
                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                </div>

                <div>
                    @if(is_null($notification->read_at))
                        <!-- Tombol untuk menandai sebagai telah dibaca -->
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Tandai Dibaca</button>
                        </form>
                    @endif


                    <!-- Tombol untuk menghapus notifikasi -->
                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                    </form>
                </div>
            </li>
            @endforeach
        </ul>
    @else
        <p>Tidak ada notifikasi.</p>
    @endif
</div>
@endsection
