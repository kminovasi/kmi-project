@extends('layouts.app')

@section('content')

<div class="container">
    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <div class="fw-bold mb-1">Gagal memperbarui status:</div>
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Detail Pengajuan L&S</h4>
        <a href="{{ route('learnshare.index') }}" class="btn btn-light">Kembali</a>
    </div>

    @php
        $raw = (array) ($learnshare->speakers ?? []);
        $employeeIds = [];
        $outs = [];
        foreach ($raw as $v) {
            if (is_string($v) && str_starts_with($v, 'OUT::')) {
                $parts = explode('::', $v);
                $outs[] = [
                    'name'        => $parts[1] ?? '',
                    'institution' => $parts[2] ?? '',
                    'title'       => $parts[3] ?? '',
                    'email'       => $parts[4] ?? '',
                ];
            } elseif (is_string($v) && $v !== '') {
                $employeeIds[] = $v;
            }
        }

        $speakerUsers = collect();
        if (!empty($employeeIds)) {
            $speakerUsers = \App\Models\User::whereIn('employee_id', $employeeIds)
                ->orderBy('name')
                ->get(['employee_id','name','unit_name','department_name']);
        }

        // FILES
        $files = [];
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn($learnshare->getTable(),'attachments') && is_array($learnshare->attachments)) {
                $files = $learnshare->attachments;
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn($learnshare->getTable(),'files') && is_array($learnshare->files)) {
                $files = $learnshare->files;
            } else {
                $files = \Illuminate\Support\Facades\Storage::disk('public')->files("learnshare/{$learnshare->id}");
            }
        } catch (\Throwable $e) {
            $files = [];
        }

        // Status badge helper
        $st = $learnshare->status ?? 'Pending';
        $badge = [
            'Approved' => 'bg-success',
            'Rejected' => 'bg-danger',
            'Pending'  => 'bg-secondary',
        ][$st] ?? 'bg-secondary';
    @endphp
    

    <div class="card">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-md-3">Judul</dt>
                <dd class="col-md-9">{{ $learnshare->title }}</dd>

                <dt class="col-md-3">Job Function</dt>
                <dd class="col-md-9">{{ $learnshare->job_function ?: '-' }}</dd>

                <dt class="col-md-3">Kompetensi</dt>
                <dd class="col-md-9">{{ $learnshare->competency ?: '-' }}</dd>

                <dt class="col-md-3">Departemen/Unit Peminta</dt>
                <dd class="col-md-9">{{ $learnshare->requesting_department }}</dd>

                <dt class="col-md-3">Waktu Pelaksanaan</dt>
                <dd class="col-md-9">{{ optional($learnshare->scheduled_at)->format('d M Y H:i') }}</dd>

                <dt class="col-md-3">Tujuan</dt>
                <dd class="col-md-9">{{ $learnshare->objective }}</dd>

                <dt class="col-md-3">Opening Speech</dt>
                <dd class="col-md-9">
                    @php
                        $opener = null;
                        if ($learnshare->opening_speech) {
                            $opener = \App\Models\User::where('employee_id',$learnshare->opening_speech)
                                        ->first(['name','employee_id','unit_name','department_name']);
                        }
                    @endphp
                    @if($opener)
                        {{ $opener->name }} — {{ $opener->employee_id }}
                        @if($opener->unit_name) ({{ $opener->unit_name }}) @endif
                        @if($opener->department_name) — {{ $opener->department_name }} @endif
                    @else
                        {{ $learnshare->opening_speech ?: '-' }}
                    @endif
                </dd>

                {{-- STATUS --}}
                <dt class="col-md-3">Status</dt>
                <dd class="col-md-9">
                    <span class="badge {{ $badge }}">{{ $st }}</span>
                    @if(!empty($learnshare->status_comment))
                        <div class="mt-2">
                            <div class="small text-muted">Alasan (terakhir):</div>
                            <div class="border rounded p-2 bg-light">{{ $learnshare->status_comment }}</div>
                        </div>
                    @endif
                </dd>

                <dt class="col-md-3">Pembicara</dt>
                <dd class="col-md-9">
                    @if($speakerUsers->isEmpty() && empty($outs))
                        -
                    @else
                        <ul class="mb-0">
                            @foreach($speakerUsers as $u)
                                <li>
                                    <span class="badge bg-success me-1">Karyawan</span>
                                    {{ $u->name }} — {{ $u->employee_id }}
                                    @if($u->unit_name) ({{ $u->unit_name }}) @endif
                                    @if($u->department_name) — {{ $u->department_name }} @endif
                                </li>
                            @endforeach
                            @foreach($outs as $o)
                                <li>
                                    <span class="badge bg-secondary me-1">Outsource</span>
                                    {{ $o['name'] ?: '-' }}
                                    @if($o['title']) — {{ $o['title'] }} @endif
                                    @if($o['institution']) ({{ $o['institution'] }}) @endif
                                    @if($o['email']) — <a href="mailto:{{ $o['email'] }}">{{ $o['email'] }}</a> @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </dd>

                <dt class="col-md-3">Peserta</dt>
                <dd class="col-md-9">
                    @if(!empty($learnshare->participants))
                        <ul class="mb-0">
                            @foreach($learnshare->participants as $p)
                                <li>{{ $p }}</li>
                            @endforeach
                        </ul>
                    @else
                        -
                    @endif
                </dd>

                <dt class="col-md-3">Lampiran</dt>
                <dd class="col-md-9">
                    @if(!empty($files))
                        <ul class="mb-0">
                            @foreach($files as $path)
                                @php
                                    $token = encrypt($path);
                                    $url   = route('learnshare.file', [$learnshare->id, $token]);
                                @endphp
                                <li><a href="{{ $url }}" target="_blank" rel="noopener">{{ basename($path) }}</a></li>
                            @endforeach
                        </ul>
                    @else
                        -
                    @endif
                </dd>

                <dt class="col-md-3">Diajukan Oleh</dt>
                <dd class="col-md-9">
                    @php $rq = $learnshare->requester; @endphp
                    @if($rq)
                        {{ $rq->name }}
                        @if($learnshare->employee_id) — {{ $learnshare->employee_id }} @endif
                    @else
                        {{ $learnshare->employee_id ?: '-' }}
                    @endif
                </dd>
            </dl>
        </div>
    </div>
</div>

{{-- ==== Admin Toolbar ==== --}}
@if(auth()->check() && auth()->user()->role === 'Superadmin')
    @push('css')
    <style>
        .status-btns .btn.disabled { pointer-events: none; opacity: .7; }
    </style>
    @endpush

    <div class="bg-light border-top py-3 mt-3">
        <div class="container">
            <div class="row g-3 align-items-end">
                <div class="col-md-3 status-btns d-flex gap-2 flex-wrap">
                    <button type="button"
                            class="btn btn-success {{ ($learnshare->status ?? 'Pending') === 'Approved' ? 'disabled' : '' }}"
                            data-status="Approved"
                            data-bs-toggle="modal"
                            data-bs-target="#statusModal"
                            onclick="window.setLsStatus && window.setLsStatus('Approved')">
                    Approved
                    </button>

                    <button type="button"
                            class="btn btn-danger {{ ($learnshare->status ?? 'Pending') === 'Rejected' ? 'disabled' : '' }}"
                            data-status="Rejected"
                            data-bs-toggle="modal"
                            data-bs-target="#statusModal"
                            onclick="window.setLsStatus && window.setLsStatus('Rejected')">
                    Rejected
                    </button>

                    <button type="button"
                            class="btn btn-secondary {{ ($learnshare->status ?? 'Pending') === 'Pending' ? 'disabled' : '' }}"
                            data-status="Pending"
                            data-bs-toggle="modal"
                            data-bs-target="#statusModal"
                            onclick="window.setLsStatus && window.setLsStatus('Pending')">
                    Pending
                    </button>

                </div>
            </div>
        </div>
    </div>

{{-- Modal Form Komentar --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('learnshare.updateStatus', $learnshare->id) }}" class="modal-content">
      @csrf
      @method('PATCH')

      <input type="hidden" name="status" id="statusInput" value="{{ old('status') }}">

      <div class="modal-header">
        <h5 class="modal-title" id="statusModalLabel">Ubah Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>

    <div class="modal-body">
    <div class="mb-2">
        <span class="small text-muted">Status terpilih:</span>
        <span id="statusBadge" class="badge">—</span>
    </div>

    <div class="mb-3">
        <label class="form-label">Komentar/Alasan <span class="text-danger">*</span></label>
        <textarea name="comment" rows="4"
        class="form-control @error('comment') is-invalid @enderror"
        placeholder="Tuliskan alasan memilih status ini..." required>{{ old('comment') }}</textarea>
        @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
        <div class="form-text">Minimal 3 karakter.</div>
    </div>
    </div>


      <div class="modal-footer d-flex justify-content-between flex-wrap gap-2">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
        <button type="submit" id="submitStatusBtn" class="btn btn-primary">
          Kirim
        </button>
      </div>
    </form>
  </div>
</div>

@push('js')
<script>
(function () {
  const statusModal = document.getElementById('statusModal');
  const statusInput = document.getElementById('statusInput');
  const modalTitle  = document.getElementById('statusModalLabel');
  const submitBtn   = document.getElementById('submitStatusBtn');
  const statusBadge = document.getElementById('statusBadge');

  function paint(st) {
    modalTitle.textContent = `Ubah Status: ${st}`;
    submitBtn.classList.remove('btn-success','btn-danger','btn-secondary','btn-primary');
    statusBadge.classList.remove('bg-success','bg-danger','bg-secondary');

    if (st === 'Approved') {
      submitBtn.classList.add('btn-success'); statusBadge.classList.add('bg-success');
    } else if (st === 'Rejected') {
      submitBtn.classList.add('btn-danger');  statusBadge.classList.add('bg-danger');
    } else {
      submitBtn.classList.add('btn-secondary'); statusBadge.classList.add('bg-secondary');
      st = 'Pending';
    }
    statusBadge.textContent = st;
  }

  window.setLsStatus = function (st) {
    if (!statusInput) return;
    statusInput.value = st;
    paint(st);
  };

  statusModal.addEventListener('show.bs.modal', function (ev) {
    const st = (ev.relatedTarget && ev.relatedTarget.getAttribute('data-status'))
               || statusInput.value || 'Pending';
    statusInput.value = st;
    paint(st);
  });

  @if ($errors->any() && old('status'))
    document.addEventListener('DOMContentLoaded', function () {
      const st = @json(old('status'));
      statusInput.value = st; paint(st);
      new bootstrap.Modal(statusModal).show();
    });
  @endif
})();
</script>
@endpush

@endif
@endsection
