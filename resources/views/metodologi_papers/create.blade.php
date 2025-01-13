@extends('layouts.app')

@section('title', 'Buat Makalah Metodologi')

@section('content')
<x-header-content title="Buat Makalah Metodologi" />
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Whoops!</strong> Ada beberapa masalah dengan input Anda.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <ul class="mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('management-system.metodologi_papers.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Nama:</label>
                            <input type="text" name="name" class="form-control shadow-sm" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label for="step" class="font-weight-bold">Langkah:</label>
                            <select name="step" class="form-control shadow-sm" required>
                                <option value="">Pilih Langkah</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="max_user" class="font-weight-bold">Maksimal Pengguna:</label>
                            <input type="number" name="max_user" class="form-control shadow-sm" placeholder="Maksimal Pengguna" min="0" required>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3 w-100 py-2">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

