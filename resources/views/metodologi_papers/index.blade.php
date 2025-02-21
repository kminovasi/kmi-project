@extends('layouts.app')

@section('title', 'Makalah Metodologi')

@section('content')
<x-header-content title="Makalah Metodologi" />
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-12">
            <a href="{{ route('management-system.metodologi_papers.create') }}" class="btn btn-primary mb-3">Buat Makalah Metodologi Baru</a>
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    @php $i = 0; @endphp
                    <table class="table table-bordered">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Langkah</th>
                            <th>Maksimal Angota</th>
                            <th width="280px">Aksi</th>
                        </tr>
                        @foreach ($metodologiPapers as $metodologiPaper)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $metodologiPaper->name }}</td>
                            <td>{{ $metodologiPaper->step }}</td>
                            <td>{{ $metodologiPaper->max_user }}</td>
                            <td>
                                <form action="{{ route('management-system.metodologi_papers.destroy', $metodologiPaper->id) }}" method="POST">
                                    <a class="btn btn-info" href="{{ route('management-system.metodologi_papers.edit', $metodologiPaper->id) }}">Edit</a>
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $metodologiPaper->id }}">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($metodologiPapers as $metodologiPaper)
<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal{{ $metodologiPaper->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $metodologiPaper->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $metodologiPaper->id }}">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus Makalah Metodologi ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('management-system.metodologi_papers.destroy', $metodologiPaper->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
