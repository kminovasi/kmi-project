@extends('layouts.app')

@section('title', 'Buat Makalah Metodologi')

@section('content')
<x-header-content title="Buat Makalah Metodologi" />
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
                    <form action="{{ route('management-system.metodologi_papers.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nama:</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama">
                        </div>
                        <div class="form-group">
                            <label for="step">Langkah:</label>
                            <div>
                                <input type="radio" id="step7" name="step" value="7">
                                <label for="step7">7</label>
                            </div>
                            <div>
                                <input type="radio" id="step8" name="step" value="8">
                                <label for="step8">8</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="max_user">Maksimal Pengguna:</label>
                            <input type="number" name="max_user" class="form-control" placeholder="Maksimal Pengguna" min="0">
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
