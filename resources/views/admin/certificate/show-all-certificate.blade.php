@extends('layouts.app')
@section('title', 'Lihat Sertifikat')

@section('content')
    @push('css')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
        <style>
            .tile-card {
                border: 0;
                border-radius: .75rem;
                box-shadow: 0 6px 18px rgba(0,0,0,.06);
                transition: transform .15s ease, box-shadow .15s ease;
                position: relative;
                overflow: hidden;
                background: #fff;
                height: 100%;
            }
            .tile-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 24px rgba(0,0,0,.10);
            }
            .tile-accent {
                position: absolute;
                left: 0; top: 0; bottom: 0;
                width: 6px;
                border-radius: .75rem 0 0 .75rem;
                opacity: .95;
            }
            .tile-accent-primary { background: #1d4ed8; }   /* biru */
            .tile-accent-success { background: #059669; }   /* hijau */

            .tile-body { padding: 1.25rem 1.25rem 1.25rem 1.5rem; }
            .tile-icon {
                width: 32px; height: 32px;
                display: inline-flex; align-items: center; justify-content: center;
                border-radius: 8px;
                background: rgba(0,0,0,.04);
                margin-bottom: .5rem;
            }
            .tile-title {
                margin: 0;
                font-weight: 700;
                font-size: 1.1rem;
            }
            .tile-desc {
                margin: .35rem 0 0;
                color: #6b7280; /* gray-500 */
                font-size: .95rem;
            }
            a.tile-link { text-decoration: none; color: inherit; display: block; }
            .text-primary-600 { color: #2563eb; }
            .text-success-600 { color: #059669; }
        </style>
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
                            Lihat Sertifikat
                        </h1>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-xl px-4">
    <div class="row g-3">
        <!-- Kartu Juri -->
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('certificates.judges.index') }}" class="tile-link">
                <div class="tile-card">
                    <span class="tile-accent tile-accent-primary"></span>
                    <div class="tile-body">
                        <div class="tile-icon">
                            <i data-feather="award" class="text-primary-600"></i>
                        </div>
                        <h5 class="tile-title text-primary-600">Juri</h5>
                        <p class="tile-desc">Unduh sertifikat juri.</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Kartu Peserta -->
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route('certificates.participants.index') }}" class="tile-link">
                <div class="tile-card">
                    <span class="tile-accent tile-accent-success"></span>
                    <div class="tile-body">
                        <div class="tile-icon">
                            <i data-feather="users" class="text-success-600"></i>
                        </div>
                        <h5 class="tile-title text-success-600">Peserta & Tim</h5>
                        <p class="tile-desc">Unduh sertifikat peserta dan Tim.</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

@endsection
