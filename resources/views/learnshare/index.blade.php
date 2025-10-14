@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pengajuan Learn & Share</h4>
        <a href="{{ route('learnshare.create') }}" class="btn btn-primary">Buat Pengajuan</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Judul</th>
                            <th>Dept/Unit Peminta</th>
                            <th>Waktu Pelaksanaan</th>
                            <th>Diajukan Oleh</th>
                            <th>Status</th> 
                             <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $i => $row)
                            <tr>
                                <td>{{ $items->firstItem() + $i }}</td>
                                <td>{{ $row->title }}</td>
                                <td>{{ $row->requesting_department }}</td>
                                <td>{{ optional($row->scheduled_at)->format('d M Y H:i') }}</td>
                                <td>{{ optional($row->requester ?? $row->user)->name ?? '-' }}</td>
                                <td>
                                    @php
                                        $st = $row->status ?? 'Pending';
                                        $badge = [
                                            'Approved' => 'bg-success',
                                            'Rejected' => 'bg-danger',
                                            'Pending'  => 'bg-secondary',
                                        ][$st] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $st }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('learnshare.show', $row->id) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('learnshare.edit', $row->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4">Belum ada pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
