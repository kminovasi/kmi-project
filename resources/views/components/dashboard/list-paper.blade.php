@extends('layouts.app')

@section('title', 'Daftar Makalah inovasi | Dashboard')

@push('css')
    <link
        href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.css"
        rel="stylesheet">
@endpush

@section('content')
<x-header-content title="{!! $category . ' - ' . strtoupper($status) !!}"></x-header-content>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <table id="eventsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th class="text-center">Judul</th>
                            <th class="text-center">Nama Team</th>
                            <th class="text-center">Perusahaan</th>
                            <th class="text-center">Status Lomba</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($hasData)
                            @foreach($categories as $category)
                                @foreach ($category->teams as $team)
                                    @foreach ($team->papers as $paper)
                                        <tr>
                                            <td class="w-50">{{ $paper->innovation_title }}</td>
                                            <td class="text-center align-middle">{{ $team->team_name}}</td>
                                            <td class="text-center align-middle">{{ $team->company->company_name ?? '-' }}</td>
                                            <td class="text-center align-middle">
                                                @if ($paper->status == 'accepted by innovation admin')
                                                <span class="badge bg-success">Penilaian</span>
                                                @else
                                                <span class="badge bg-secondary">Melengkapi Paper dan Benefit</span>
                                                @endif
                                            </td>
                                        </tr>                       
                                    @endforeach
                                @endforeach
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script
        src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/cr-2.0.4/date-1.5.4/fc-5.0.4/fh-4.0.1/kt-2.12.1/r-3.0.3/rg-1.5.0/rr-1.5.0/sc-2.4.3/sb-1.8.1/sp-2.3.3/sl-2.1.0/sr-1.4.1/datatables.min.js">
    </script>

    <script>
        
    </script>
@endpush
