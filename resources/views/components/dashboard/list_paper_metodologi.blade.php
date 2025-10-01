@extends('layouts.app')
@section('title', 'Metodologi - '.$metodologi->name)

@push('css')
<link
  href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/r-3.0.3/datatables.min.css"
  rel="stylesheet">
@endpush

@section('content')
  <x-header-content :title="strtoupper($metodologi->name).' - METODOLOGI'"></x-header-content>

  <div class="container mt-4">
    <div class="card">
      <div class="card-body">
        <table id="metodologiTable" class="table table-striped w-100">
          <thead>
            <tr>
              <th class="text-center">Judul</th>
              <th class="text-center">Nama Team</th>
              <th class="text-center">Perusahaan</th>
              <th class="text-center">Status Lomba</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($papers as $p)
              <tr>
                <td class="w-50">{{ $p->title }}</td>
                <td class="text-center align-middle">{{ strtoupper($p->team_name) }}</td>
                <td class="text-center align-middle">{{ $p->company_name }}</td>
                <td class="text-center align-middle">
                  @if ($p->status === 'accepted by innovation admin')
                    <span class="badge bg-success">Penilaian</span>
                  @else
                    <span class="badge bg-secondary">Melengkapi Paper dan Benefit</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center">Tidak ada data</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script
  src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.8/b-3.1.2/b-colvis-3.1.2/b-html5-3.1.2/b-print-3.1.2/r-3.0.3/datatables.min.js">
</script>

<script>
  $(function () {
    $('#metodologiTable').DataTable({
    responsive: true,
    pageLength: 25,
    lengthMenu: [10, 25, 50, 100],
    order: [[0, 'asc']],
    pagingType: 'simple_numbers', 
    dom:
        "<'row mb-2'<'col-md-6'l><'col-md-6 text-end'f>>" +
        "tr" +
        "<'row mt-2'<'col-md-6'i><'col-md-6 d-flex justify-content-end'p>>",
    language: {
        search: "Cari:",
        lengthMenu: "Tampil _MENU_ baris",
        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
        infoFiltered: "(difilter dari _MAX_ total data)",
        zeroRecords: "Tidak ada data yang cocok",
        paginate: { previous: "Sebelumnya", next: "Berikutnya" }
    }
    });
  });
</script>
@endpush
