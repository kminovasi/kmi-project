@extends('layouts.app')
@section('title', 'Edit Paper | ' . $paper->innovation_title)

@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="edit"></i></div>
                            Edit Paper
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <a class="btn btn-sm btn-light text-primary" href="{{ route('event-team.show', $eventId) }}">
                            <i class="me-1" data-feather="arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-xl px-4 mt-4">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-xl-12">
                <div class="card mb-4">
                    <div class="card-header">Paper Information</div>
                    <div class="card-body">
                        <form action="{{ route('event-team.paper.update', ['id' => $paper->id, 'eventId' => $eventId]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="small mb-1" for="inovasi_lokasi">Inovasi Lokasi</label>
                                <input class="form-control @error('inovasi_lokasi') is-invalid @enderror"
                                    id="inovasi_lokasi" name="inovasi_lokasi" type="text"
                                    value="{{ old('inovasi_lokasi', $paper->inovasi_lokasi) }}" required>
                                @error('inovasi_lokasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="full_paper">Full Paper</label>
                                <input class="form-control @error('full_paper') is-invalid @enderror" id="full_paper"
                                    name="full_paper" type="file">
                                @if ($paper->full_paper)
                                    <small class="text-muted">Current file: {{ $paper->full_paper }}</small>
                                @endif
                                @error('full_paper')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="abstract">Abstract</label>
                                <textarea class="form-control @error('abstract') is-invalid @enderror" id="abstract" name="abstract" rows="4"
                                    required>{{ old('abstract', $paper->abstract) }}</textarea>
                                @error('abstract')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="problem">Problem</label>
                                <textarea class="form-control @error('problem') is-invalid @enderror" id="problem" name="problem" rows="4"
                                    required>{{ old('problem', $paper->problem) }}</textarea>
                                @error('problem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="main_cause">Main Cause</label>
                                <textarea class="form-control @error('main_cause') is-invalid @enderror" id="main_cause" name="main_cause"
                                    rows="4" required>{{ old('main_cause', $paper->main_cause) }}</textarea>
                                @error('main_cause')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="solution">Solution</label>
                                <textarea class="form-control @error('solution') is-invalid @enderror" id="solution" name="solution" rows="4"
                                    required>{{ old('solution', $paper->solution) }}</textarea>
                                @error('solution')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="innovation_photo">Innovation Photo</label>
                                <input class="form-control @error('innovation_photo') is-invalid @enderror"
                                    id="innovation_photo" name="innovation_photo" type="file">
                                @if ($paper->innovation_photo)
                                    <small class="text-muted">Current file: {{ $paper->innovation_photo }}</small>
                                @endif
                                @error('innovation_photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="proof_idea">Proof of Idea</label>
                                <input class="form-control @error('proof_idea') is-invalid @enderror" id="proof_idea"
                                    name="proof_idea" type="file">
                                @if ($paper->proof_idea)
                                    <small class="text-muted">Current file: {{ $paper->proof_idea }}</small>
                                @endif
                                @error('proof_idea')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="status_inovasi">Status Inovasi</label>
                                <select class="form-control @error('status_inovasi') is-invalid @enderror"
                                    id="status_inovasi" name="status_inovasi" required>
                                    <option value="Not Implemented"
                                        {{ old('status_inovasi', $paper->status_inovasi) == 'Not Implemented' ? 'selected' : '' }}>
                                        Not Implemented</option>
                                    <option value="Progress"
                                        {{ old('status_inovasi', $paper->status_inovasi) == 'Progress' ? 'selected' : '' }}>
                                        Progress</option>
                                    <option value="Implemented"
                                        {{ old('status_inovasi', $paper->status_inovasi) == 'Implemented' ? 'selected' : '' }}>
                                        Implemented</option>
                                </select>
                                @error('status_inovasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="small mb-1" for="potensi_replikasi">Potensi Replikasi</label>
                                <select class="form-control @error('potensi_replikasi') is-invalid @enderror"
                                    id="potensi_replikasi" name="potensi_replikasi" required>
                                    <option value="Bisa Direplikasi"
                                        {{ old('potensi_replikasi', $paper->potensi_replikasi) == 'Bisa Direplikasi' ? 'selected' : '' }}>
                                        Bisa Direplikasi</option>
                                    <option value="Tidak Bisa Direplikasi"
                                        {{ old('potensi_replikasi', $paper->potensi_replikasi) == 'Tidak Bisa Direplikasi' ? 'selected' : '' }}>
                                        Tidak Bisa Direplikasi</option>
                                </select>
                                @error('potensi_replikasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button class="btn btn-primary" type="submit">Update Paper</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
