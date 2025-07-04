@extends('layouts.app')
@section('title', 'Template Sertifikat')
@section('content')
    @push('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    @endpush
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush

    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="image"></i></div>
                            Template Sertifikat
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-xl px-4 mt-4">

        {{-- Menampilkan pesan sukses --}}
        <x-toast-alert type="success" message="{{ session('success') }}" />
        {{-- Menampilkan pesan error --}}
        <x-toast-alert type="danger" message="{{ $errors->first('error') }}" />

        <div class="card">
            <div class="card-header d-flex justify-content-end">
                <button type="button" class="btn btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i data-feather="upload" class="me-2"></i> Upload Sertifikat
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="font-weight: normal;">No</th>
                                <th scope="col" style="font-weight: normal;">Event</th>
                                <th scope="col" style="font-weight: normal;">Perusahaan</th>
                                <th scope="col" style="font-weight: normal;">Template Gambar</th>
                                <th scope="col" style="font-weight: normal;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Contoh data sertifikat -->
                            @if ($certificates->isEmpty())
                                <tr class="text-center">
                                    <td colspan="5">Data Tidak Ditemukan</td>
                                </tr>
                            @else
                                @foreach ($certificates as $key => $certificate)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $certificate->event->event_name }} {{ $certificate->event->year }}</td>
                                        <td>{{ $certificate->event && $certificate->event->company ? $certificate->event->company->company_name : '-' }}</td>
                                        <td>
                                            <img src="{{ asset('storage/' . $certificate->template_path) }}"
                                                alt="Template Gambar" class="img-fluid" style="max-width: 100px; height: auto;">
                                        </td>
                                        <td>
                                            <!-- Button View -->
                                            <a href="{{ asset('storage/' . $certificate->template_path) }}"
                                                class="btn btn-sm btn-info" target="_blank" title="Lihat Sertifikat">
                                                 <i data-feather="eye"></i>
                                             </a>

                                            <!-- Button Delete -->
                                            <form id="delete-form-{{ $certificate->id }}"
                                                  action="{{ route('certificates.destroy', $certificate->id) }}"
                                                  method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $certificate->id }})">
                                                    <i data-feather="trash"></i>
                                                </button>
                                            </form>

                                            <script>
                                                function confirmDelete(certificateId) {
                                                    Swal.fire({
                                                        title: 'Apakah kamu yakin?',
                                                        text: "Sertifikat ini akan dihapus secara permanen!",
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#3085d6',
                                                        cancelButtonColor: '#d33',
                                                        confirmButtonText: 'Ya, hapus!',
                                                        cancelButtonText: 'Batal'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            document.getElementById('delete-form-' + certificateId).submit();
                                                        }
                                                    });
                                                }
                                            </script>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Upload -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('certificates.store') }}" enctype="multipart/form-data" method="POST" class="needs-validation" novalidate>
                @method('post')
                @csrf
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h5 class="modal-title fw-bold" id="uploadModalLabel">Upload Sertifikat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="event_id" class="form-label">Event</label>
                            <select class="form-select" name="event_id" id="event_id" required>
                                <option value="" selected disabled>Pilih Event</option>
                                @foreach ($eventsWithoutCertificate as $event)
                                    <option value="{{ $event->event_id }}">{{ $event->event_name }} {{ $event->year }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Silakan pilih event.</div>
                        </div>
                        <div class="mb-3">
                            <label for="template_certificate" class="form-label">Upload Template Sertifikat</label>
                            <input type="file" class="form-control" name="template_certificate" id="template_certificate" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="badge_rank_1" class="form-label">Upload Badge Rank 1</label>
                            <input type="file" class="form-control" name="badge_rank_1" id="badge_rank_1" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="badge_rank_2" class="form-label">Upload Badge Rank 2</label>
                            <input type="file" class="form-control" name="badge_rank_2" id="badge_rank_2" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="badge_rank_3" class="form-label">Upload Badge Rank 3</label>
                            <input type="file" class="form-control" name="badge_rank_3" id="badge_rank_3" accept="image/*" required>
                        </div>
                        <small class="form-text">Unggah File Gambar (JPG, PNG) Maks: 5MB</small>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Tambah</button>
                    </div>
                </div>
            </form>
        </div>
    </div>




@endsection
@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 3000
                });
            });
            toastList.forEach(toast => toast.show());
        });
    </script>
@endpush
