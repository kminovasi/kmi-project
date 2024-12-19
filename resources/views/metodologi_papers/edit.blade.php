@extends('layouts.app')

@section('title', 'Edit Makalah Metodologi')

@section('content')
<x-header-content title="Edit Makalah Metodologi" />
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> Ada beberapa masalah dengan input Anda.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('management-system.metodologi_papers.update', $metodologiPaper->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Nama:</label>
                            <input type="text" name="name" class="form-control" value="{{ $metodologiPaper->name }}" placeholder="Nama">
                        </div>
                        <div class="form-group">
                            <label for="step">Langkah:</label>
                            <input type="number" name="step" class="form-control" value="{{ $metodologiPaper->step }}" placeholder="Langkah">
                        </div>
                        <div class="form-group">
                            <label for="max_user">Maksimal Pengguna:</label>
                            <input type="number" name="max_user" class="form-control" value="{{ $metodologiPaper->max_user }}" placeholder="Maksimal Pengguna">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
