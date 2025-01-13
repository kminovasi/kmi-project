@extends('layouts.app')
@section('title', 'Berita Acara')
@push('css')
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="{{asset('template/dist/css/styles.css')}}" rel="stylesheet" />
        <link rel="icon" type="image/x-icon" href="{{asset('template/dist/assets/img/favicon.png')}}" />
        <script data-search-pseudo-elements defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/js/all.min.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js" crossorigin="anonymous"></script>

@endpush
@section('content')
    <header class="page-header page-header-compact page-header-light border-bottom bg-white mb-4">
        <div class="container-xl px-4">
            <div class="page-header-content">
                <div class="row align-items-center justify-content-between pt-3">
                    <div class="col-auto mb-3">
                        <h1 class="page-header-title">
                            <div class="page-header-icon"><i data-feather="book"></i></div>
                            Daftar Berita Acara
                        </h1>
                    </div>
                    <div class="col-12 col-xl-auto mb-3">
                        <button class="btn btn-sm btn-light text-primary" type="button" data-bs-toggle="modal" data-bs-target="#createBeritaAcara">
                            <i class="me-1" data-feather="plus"></i>
                            Tambah Berita Acara
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Main page content-->
    <div class="container-xl px-4 mt-4">
        <div class="mb-3">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
                {{ session('success') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('errors'))
            <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
                {{ session('errors') }}
                <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        </div>
        <div class="card mb-4">
            <div class="card-header">Berita Acara</div>
            <div class="card-body">
                <table id="datatablesSimple">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Event</th>
                            <th>Tahun</th>
                            <th>No. Surat</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $no = 1;
                        ?>
                        @foreach ($data as $d)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>{{$d->event_name}}</td>
                            <td>{{$d->year}}</td>
                            <td>{{$d->no_surat}}</td>
                            <td><a href="{{route('berita-acara.showPDF', ['id' => $d->id])}}" class="btn btn-info btn-sm" target="_blank">Show</a>
                                <!-- <a href="{{route('berita-acara.downloadPDF', ['id' => $d->id])}}" class="btn btn-primary btn-sm">Download</a></td> -->
                                {{-- <a href="{{route('berita-acara.downloadPDF', ['id' => $d->id])}}" class="btn btn-primary btn-sm">Download</a></td> --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Modal-->
        <div class="modal fade" id="createBeritaAcara" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Form Berita Acara</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{route('berita-acara.store')}}" method="POST" >
                        @csrf
                        @method('POST')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="chooseEvent">Pilih Event</label>
                                <select name="event_id" id="chooseEvent" class="form-control">
                                    @foreach ($event as $item)
                                        <option value="{{$item->id}}">{{$item->event_name}} - {{$item->year}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="inputNoSurat">No Surat</label>
                                <input type="text" name="no_surat" id="inputNoSurat" placeholder="Masukkan Nomor Surat" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="chooseJenisEvent">Jenis Event</label>
                                <select name="jenis_event" id="chooseJenisEvent" class="form-control">
                                    <option value="Internal">Internal</option>
                                    <option value="Group">Group</option>
                                    <option value="Eksternal">Eksternal</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="inputDate">Tanggal Penetapan Juara</label>
                                <input type="date" name="penetapan_juara" id="inputDate" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                            <button class="btn btn-primary" type="submit">Save changes</button>
                        </div>
                     </form>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{asset('template/dist/js/scripts.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="{{asset('template/dist/js/datatables/datatables-simple-demo.js')}}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endpush
