@extends('layouts.app')

@section('content')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
  #ls-speakers-wrap .speaker-row{ border:1px dashed #e2e8f0; border-radius:.5rem; padding:.75rem; margin-bottom:.5rem;}
  #ls-speakers-wrap .row-actions{ gap:.5rem;}
  .select2-container{ width:100%!important;}
</style>
@endpush

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Pengajuan L&S</h4>
        <a href="{{ route('learnshare.show', $learnshare->id) }}" class="btn btn-light">Kembali</a>
    </div>

    <form action="{{ route('learnshare.update', $learnshare->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="card mb-3">
            <div class="card-body">

                {{-- Judul --}}
                <div class="mb-3">
                    <label class="form-label">Judul <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $learnshare->title) }}">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Job Function --}}
                <div class="mb-3">
                    <label class="form-label">Job Function</label>
                    <input type="text" name="job_function" class="form-control @error('job_function') is-invalid @enderror"
                           value="{{ old('job_function', $learnshare->job_function) }}">
                    @error('job_function') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Kompetensi --}}
                <div class="mb-3">
                    <label class="form-label">Kompetensi</label>
                    <input type="text" name="competency" class="form-control @error('competency') is-invalid @enderror"
                           value="{{ old('competency', $learnshare->competency) }}">
                    @error('competency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Dept Peminta --}}
                <div class="mb-3">
                    <label class="form-label">Departemen / Unit Peminta <span class="text-danger">*</span></label>
                    <input type="text" name="requesting_department" class="form-control @error('requesting_department') is-invalid @enderror"
                           value="{{ old('requesting_department', $learnshare->requesting_department) }}">
                    @error('requesting_department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Waktu (Superadmin bisa ubah) --}}
                <div class="mb-3">
                    <label class="form-label">Waktu Pelaksanaan</label>
                    <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror"
                           value="{{ old('scheduled_at', optional($learnshare->scheduled_at)->format('Y-m-d\TH:i')) }}"
                           {{ auth()->user()->role === 'Superadmin' ? '' : 'disabled' }}>
                    @if(auth()->user()->role !== 'Superadmin')
                        <small class="text-muted">Hubungi Superadmin untuk mengubah tanggal.</small>
                    @endif
                    @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Tujuan --}}
                <div class="mb-3">
                    <label class="form-label">Tujuan <span class="text-danger">*</span></label>
                    <textarea name="objective" rows="3" class="form-control @error('objective') is-invalid @enderror">{{ old('objective', $learnshare->objective) }}</textarea>
                    @error('objective') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Opening Speech (karyawan) --}}
                <div class="mb-3">
                    <label class="form-label">Opening Speech (Karyawan)</label>
                    <select class="form-select" id="ls-opening-select" name="opening_speech_employee_id"></select>
                    @error('opening_speech_employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- Pembicara (dinamis employee/outsource) --}}
                <div class="mb-3" id="ls-speakers-section">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="small mb-1">Pembicara</h6>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="btnAddSpeaker">+ Tambah Pembicara</button>
                    </div>
                    <div id="ls-speakers-wrap"></div>
                    <small class="text-muted d-block mt-2">Klik <em>Eksternal</em> jika pembicara dari luar.</small>
                </div>

                {{-- Peserta --}}
                <div class="mb-3">
                    <label class="form-label">Peserta</label>
                    <div id="participantsWrap">
                        @php $parts = old('participants', (array)($learnshare->participants ?? [''])); @endphp
                        @foreach($parts as $i => $ps)
                            <div class="input-group mb-2 participant-row">
                                <input type="text" name="participants[]" class="form-control" value="{{ $ps }}" placeholder="Nama/Unit">
                            </div>
                        @endforeach
                        <button class="btn btn-sm btn-outline-secondary" type="button"
                                onclick="document.getElementById('participantsWrap').insertAdjacentHTML('beforeend', `<div class='input-group mb-2 participant-row'><input type='text' name='participants[]' class='form-control' placeholder='Nama/Unit'></div>`)">
                            + Tambah Peserta
                        </button>
                    </div>
                </div>

                {{-- Lampiran: hapus & tambah --}}
                <div class="mb-3">
                    <label class="form-label">Lampiran Saat Ini</label>
                    @if(!empty($files))
                        <ul class="list-unstyled">
                            @foreach($files as $path)
                                <li class="d-flex align-items-center gap-2">
                                    <div>
                                        @php
                                            $token = encrypt($path);
                                            $url   = route('learnshare.file', [$learnshare->id, $token]);
                                        @endphp
                                        <a href="{{ $url }}" target="_blank">{{ basename($path) }}</a>
                                    </div>
                                    <label class="ms-2 text-danger small">
                                        <input type="checkbox" name="delete_files[]" value="{{ $path }}">
                                        Hapus
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-muted">Belum ada lampiran.</div>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="form-label">Tambah Lampiran</label>
                    <input type="file" class="form-control" name="ls_files[]" multiple
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.png,.jpg,.jpeg,.zip,.rar">
                    <small class="text-muted">PDF/DOC/PPT/XLS/IMG/ZIP (maks 10MB/berkas).</small>
                    @error('ls_files') <div class="text-danger small">{{ $message }}</div> @enderror
                    @error('ls_files.*') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- Status & Komentar (hanya Superadmin) --}}
                {{-- @if(auth()->user()->role === 'Superadmin')
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            @foreach(['Pending','Approved','Rejected'] as $s)
                                <option value="{{ $s }}" {{ old('status', $learnshare->status ?? 'Pending')===$s?'selected':'' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                    </div> --}}
                    {{-- <div class="mb-3">
                        <label class="form-label">Komentar / Alasan</label>
                        <textarea name="status_comment" rows="3" class="form-control">{{ old('status_comment', $learnshare->status_comment) }}</textarea>
                        <small class="text-muted">Wajib diisi saat mengubah status agar terarsip alasan keputusan.</small>
                    </div> --}}
                {{-- @endif --}}

                <div class="d-flex justify-content-end">
                    <a href="{{ route('learnshare.index', $learnshare->id) }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
(function(){
  let idx = 0;

  function rowTplEmployee(i, selectedId = null){
    const sel = selectedId ? `<option value="${selectedId}" selected>${selectedId}</option>` : '';
    return `
      <div class="speaker-row" data-index="${i}">
        <input type="hidden" name="speakers_payload[${i}][type]" value="employee">
        <div class="mb-2">
          <select class="form-select ls-employee-select" name="speakers_payload[${i}][employee_id]" id="ls_emp_${i}" data-index="${i}">
            ${sel}
          </select>
        </div>
        <div class="d-flex row-actions">
          <button class="btn btn-sm btn-outline-secondary btnToOutsource" type="button" data-index="${i}">Eksternal</button>
          <button class="btn btn-sm btn-outline-danger btnRemoveRow" type="button" data-index="${i}">Hapus</button>
        </div>
      </div>`;
  }

  function rowTplOutsource(i, vals = {}){
    return `
      <div class="speaker-row" data-index="${i}">
        <input type="hidden" name="speakers_payload[${i}][type]" value="outsource">
        <div class="row g-2">
          <div class="col-md-3"><input class="form-control" name="speakers_payload[${i}][name]" placeholder="Nama" value="${vals.name??''}" required></div>
          <div class="col-md-3"><input class="form-control" name="speakers_payload[${i}][institution]" placeholder="Instansi" value="${vals.institution??''}" required></div>
          <div class="col-md-3"><input class="form-control" name="speakers_payload[${i}][title]" placeholder="Jabatan" value="${vals.title??''}" required></div>
          <div class="col-md-3"><input class="form-control" type="email" name="speakers_payload[${i}][email]" placeholder="Email" value="${vals.email??''}" required></div>
        </div>
        <div class="d-flex row-actions mt-2">
          <button class="btn btn-sm btn-outline-secondary btnToEmployee" type="button" data-index="${i}">Karyawan</button>
          <button class="btn btn-sm btn-outline-danger btnRemoveRow" type="button" data-index="${i}">Hapus</button>
        </div>
      </div>`;
  }

  function initSelect2($el){
    $el.select2({
      width:'100%',
      placeholder:'Cari karyawan…',
      allowClear:true,
      ajax:{
        headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type:'POST',
        url: "{{ route('query.autocomplete') }}",
        dataType:'json',
        delay:250,
        data:(params)=>({ query: params.term || '' }),
        processResults:(data)=>({
          results: $.map(data, function(item){
            return { id: item.employee_id, text: (item.employee_id ?? '-') + ' - ' + (item.name ?? '-') + (item.company_name ? ' -- '+item.company_name : '') };
          })
        }),
        cache:true
      }
    });
  }

  // restore existing speakers from PHP
  const existingSpeakers = @json((array)($learnshare->speakers ?? []));
  if (existingSpeakers.length){
    existingSpeakers.forEach(v=>{
      const i = idx++;
      if (typeof v === 'string' && v.startsWith('OUT::')){
        const p = v.split('::');
        $('#ls-speakers-wrap').append(rowTplOutsource(i, {
          name: p[1]||'', institution: p[2]||'', title: p[3]||'', email: p[4]||''
        }));
      } else {
        $('#ls-speakers-wrap').append(rowTplEmployee(i, v));
        initSelect2($('#ls_emp_'+i));
      }
    });
  } else {
    // fallback 1 baris employee
    const i = idx++;
    $('#ls-speakers-wrap').append(rowTplEmployee(i));
    initSelect2($('#ls_emp_'+i));
  }

  // add new
  $('#btnAddSpeaker').on('click', function(){
    const i = idx++;
    $('#ls-speakers-wrap').append(rowTplEmployee(i));
    initSelect2($('#ls_emp_'+i));
  });

  // switch
  $(document).on('click', '.btnToOutsource', function(){
    const i = $(this).data('index');
    $(`.speaker-row[data-index="${i}"]`).replaceWith(rowTplOutsource(i, {}));
  });
  $(document).on('click', '.btnToEmployee', function(){
    const i = $(this).data('index');
    $(`.speaker-row[data-index="${i}"]`).replaceWith(rowTplEmployee(i));
    initSelect2($('#ls_emp_'+i));
  });
  $(document).on('click', '.btnRemoveRow', function(){
    const i = $(this).data('index');
    $(`.speaker-row[data-index="${i}"]`).remove();
  });

  // Opening speech preload
  initSelect2($('#ls-opening-select'));
  @if($learnshare->opening_speech)
    (function(){
      const v = @json($learnshare->opening_speech);
      const opt = new Option(v, v, true, true);
      $('#ls-opening-select').append(opt).trigger('change');
    })();
  @endif
})();
</script>
@endpush
