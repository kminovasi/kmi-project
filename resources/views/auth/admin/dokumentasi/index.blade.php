@extends('layouts.app')
@section('title', 'Dokumentasi')
@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <style type="text/css">
        .active-link {
            color: #ffc004;
            background-color: #e81500;
        }
    </style>
@endpush
@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">

            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Dokumentasi Makalah Inovasi
                        </h1>
                    </div>

                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-xl-4 mb-4">
                        {{-- Card Evidence --}}
                        <a class="card h-100 lift border-start-lg border-start-primary"
                            href="{{ route('evidence.index') }}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="me-3">
                                        <i class="feather-lg text-primary mb-3" data-feather="award"></i>
                                        <h5 class="text-primary">Makalah</h5>
                                        <div class="text-muted small">
                                            Kumpulan makalah, list kepersetaan, serta dokumen atau informasi pendukung
                                            lainnya
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 mb-4">
                        {{-- Card Berita Acara --}}
                        <a class="card h-100 lift border-start-lg border-start-success"
                            href="{{ route('dokumentasi.berita-acara.index') }}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="me-3">
                                        <i class="feather-lg text-success mb-3" data-feather="file"></i>
                                        <h5 class="text-success">Berita Acara</h5>
                                        <div class="text-muted small">
                                            Surat berita acara penetapan pemenang yang telah ditandatangani oleh BOD
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 mb-4">
                        <!-- Card Riwayat Kompetisi Peserta-->
                        <a class="card lift border-start-lg border-start-warning" href="{{ route('cv.index') }}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="me-3">
                                        <i class="fas fa-trophy fa-lg text-warning mb-3"></i>
                                        <h5 class="text-warning">Prestasi Peserta</h5>
                                        <div class="text-muted small">
                                            Data prestasi & download sertifikat Peserta. </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 mb-4">
                        {{-- Card Informasi --}}
                        <a class="card lift border-start-lg border-start-secondary" href="#">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="me-3">
                                        <i class="feather-lg text-secondary mb-3" data-feather="info"></i>
                                        <h5 class="text-secondary">Informasi</h5>
                                        {{-- <div class="text-muted small">
                                        Pengelola Inovasi masing-masing perusahaan yang akan memnentukan role pada
                                        Inovator, Juri,
                                        BOD serta menentukan aturan kegiatan kompetisi inovasi
                                    </div> --}}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 mb-4">
                        {{-- Card News --}}
                        <a class="card lift border-start-lg border-start-secondary" href="#">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="me-3">
                                        <i class="feather-lg text-secondary mb-3" data-feather="mail"></i>
                                        <h5 class="text-secondary">News</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-xl-4 mb-4">
                        {{-- Card Couching Clinic --}}
                        <a class="card lift border-start-lg border-start-primary" href="#">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="me-3">
                                        <i class="feather-lg text-primary mb-3" data-feather="info"></i>
                                        <h5 class="text-primary">Couching Clinic</h5>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                     <div class="col-xl-4 mb-4">
                        {{-- Card Prosedur --}}
                        <a class="card lift border-start-lg border-start-secondary" href="{{ route('prosedur.index') }}">
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="me-3">
                                        <i class="feather-lg text-secondary mb-3" data-feather="info"></i>
                                        <h5 class="text-secondary">Prosedur</h5>
                                        {{-- <div class="text-muted small">
                                        Pengelola Inovasi masing-masing perusahaan yang akan memnentukan role pada
                                        Inovator, Juri,
                                        BOD serta menentukan aturan kegiatan kompetisi inovasi
                                    </div> --}}
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endpush
