@extends('layouts.app')
@section('title','Daftar Pengajuan Replikasi')

@section('content')
<div class="container-xl px-4 py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h4 class="mb-0">Daftar Pengajuan Replikasi</h4>

    @if($isSuper ?? false)
      <div class="btn-group">
        <a href="{{ route('replications.index', ['status'=>'pending'])  }}"  class="btn btn-sm {{ ($status ?? '')==='pending'  ? 'btn-warning' : 'btn-outline-warning' }}">Pending</a>
        <a href="{{ route('replications.index', ['status'=>'approved']) }}" class="btn btn-sm {{ ($status ?? '')==='approved' ? 'btn-success' : 'btn-outline-success' }}">Approved</a>
        <a href="{{ route('replications.index', ['status'=>'rejected']) }}" class="btn btn-sm {{ ($status ?? '')==='rejected' ? 'btn-danger' : 'btn-outline-danger' }}">Rejected</a>
      </div>
    @endif
  </div>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
  @if(session('warning')) <div class="alert alert-warning">{{ session('warning') }}</div> @endif

  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No.</th>
            <th>Judul Inovasi</th>
            <th>Tim</th>
            <th>PIC</th>
            <th>Tanggal Rencana</th>
            <th>Status</th>
            @if($isSuper ?? false)
              <th style="width:220px">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @forelse($replications as $i => $r)
            <tr>
              <td>{{ $replications->firstItem() + $i }}</td>
              <td class="normal">{{ $r->innovation_title }}</td>
              <td>{{ $r->team->team_name ?? '—' }}</td>
              <td>
                <div>{{ $r->pic_name }}</div>
                <small class="text-muted">{{ $r->pic_phone }}</small>
              </td>
              <td>{{ $r->planned_date ? $r->planned_date->format('d M Y') : '—' }}</td>
              <td>
                @php $badge = ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$r->status] ?? 'secondary'; @endphp
                <span class="badge bg-{{ $badge }}">{{ ucfirst($r->status) }}</span>
              </td>

              @if($isSuper ?? false)
                <td>
                  @if($r->status === 'pending')
                    <div class="d-flex gap-2">
                      <form method="POST" action="{{ route('replications.approve', $r->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-success"
                                onclick="return confirm('Setujui pengajuan ini?')">
                          <i class="fas fa-check me-1"></i> Approve
                        </button>
                      </form>
                      <form method="POST" action="{{ route('replications.reject', $r->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Tolak pengajuan ini?')">
                          <i class="fas fa-times me-1"></i> Reject
                        </button>
                      </form>
                    </div>
                  @else
                    <small class="text-muted">Sudah diproses</small>
                  @endif
                </td>
              @endif
            </tr>
          @empty
            <tr><td colspan="{{ ($isSuper ?? false) ? 7 : 6 }}" class="text-center text-muted">Tidak ada data.</td></tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-2">
        {{ $replications->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
