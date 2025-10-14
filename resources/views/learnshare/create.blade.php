@extends('layouts.app')

@section('content')
@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
  #ls-speakers-wrap .speaker-row{ border:1px dashed #e2e8f0; border-radius: .5rem; padding: .75rem; margin-bottom: .5rem;}
  #ls-speakers-wrap .row-actions{ gap:.5rem; }
  .select2-container{ width:100%!important; }
</style>
@endpush

<div class="container">
    <h4 class="mb-3">Form Pengajuan Learn & Share</h4>

    <form action="{{ route('learnshare.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card mb-3">
            <div class="card-body">
                {{-- JUDUL LEARN AND SHARE --}}
                <div class="mb-3">
                    <label class="form-label">Judul Learn & Share <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title') }}" placeholder="Contoh: Semua Bisa Ngonten">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- JOB FUNCTION --}}
                <div class="mb-3">
                    <label class="form-label">Job Function</label>
                    <input type="text" name="job_function" class="form-control @error('job_function') is-invalid @enderror"
                           value="{{ old('job_function') }}" placeholder="Contoh: Digital Branding, Product Branding, Corporate Branding, Brand Campaign, Content Marketing">
                    @error('job_function') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- KOMPETENSI --}}
                <div class="mb-3">
                    <label class="form-label">Kompetensi</label>
                    <input type="text" name="competency" class="form-control @error('competency') is-invalid @enderror"
                           value="{{ old('competency') }}" placeholder="Contoh: Brand and Product Management">
                    @error('competency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- DEPARTEMEN / UNIT KERJA PEMINTA --}}
                <div class="mb-3">
                <label class="form-label">Departemen / Unit Kerja Peminta <span class="text-danger">*</span></label>
                <select name="requesting_department"
                        id="org-select"
                        class="form-select @error('requesting_department') is-invalid @enderror"></select>
                @error('requesting_department') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- WAKTU PELAKSANAAN --}}
                <div class="mb-3">
                    <label class="form-label">Waktu Pelaksanaan <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="scheduled_at" class="form-control @error('scheduled_at') is-invalid @enderror"
                           value="{{ old('scheduled_at') }}">
                    @error('scheduled_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- TUJUAN LEARN AND SHARE --}}
                <div class="mb-3">
                    <label class="form-label">Tujuan Learn & Share <span class="text-danger">*</span></label>
                    <textarea name="objective" rows="3" class="form-control @error('objective') is-invalid @enderror"
                              placeholder="Jelaskan tujuan acara...">{{ old('objective') }}</textarea>
                    @error('objective') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- OPENING SPEECH --}}
                <div class="mb-3">
                    <label class="form-label">Opening Speech (Karyawan)</label>
                    <select class="form-select" id="ls-opening-select" name="opening_speech_employee_id"></select>
                    @error('opening_speech_employee_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                {{-- PEMBICARA --}}
                <div class="mb-3" id="ls-speakers-section">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="small mb-1 mb-0">Pembicara Learn & Share</h6>
                        <div>
                        <button class="btn btn-sm btn-outline-primary" type="button" id="btnAddSpeaker">+ Tambah Pembicara</button>
                        </div>
                    </div>

                    <div id="ls-speakers-wrap"></div>

                    <small class="text-muted d-block mt-2">Klik tombol <em>Eksternal</em> Jika pembicara dari Eksternal</small>

                    @error('speakers') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                {{-- PESERTA LEARN AND SHARE (dinamis) --}}
                <div class="mb-3">
                    <label class="form-label d-flex justify-content-between">
                        <span>Peserta Learn & Share</span>
                    </label>
                    <div id="participantsWrap">
                        @php $participants = old('participants', ['']); @endphp
                        @foreach($participants as $i => $ps)
                            <div class="input-group mb-2 participant-row">
                                <input type="text" name="participants[]" class="form-control" value="{{ $ps }}" placeholder="Nama/Unit">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- UNGGAH FILE --}}
                <div class="mb-3">
                    <h6 class="small mb-1">Lampiran Learn & Share</h6>
                    <input type="file" class="form-control" name="ls_files[]" multiple
                            accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.png,.jpg,.jpeg,.zip,.rar">
                    <small class="text-muted">PDF/DOC/PPT/XLS/IMG/ZIP (maks 10MB/berkas).</small>
                    @error('ls_files') <div class="text-danger small">{{ $message }}</div> @enderror
                    @error('ls_files.*') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('learnshare.index') }}" class="btn btn-light me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Ajukan L&S</button>
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

        function rowTplEmployee(i){
            return `
            <div class="speaker-row" data-index="${i}">
                <input type="hidden" name="speakers_payload[${i}][type]" value="employee">
                <div class="mb-2">
                <select class="form-select ls-employee-select" name="speakers_payload[${i}][employee_id]" id="ls_emp_${i}" data-index="${i}"></select>
                </div>
                <div class="d-flex row-actions">
                <button class="btn btn-sm btn-outline-secondary btnToOutsource" type="button" data-index="${i}">Eksternal</button>
                <button class="btn btn-sm btn-outline-danger btnRemoveRow" type="button" data-index="${i}">Hapus</button>
                </div>
            </div>`;
        }

        function rowTplOutsource(i){
            return `
            <div class="speaker-row" data-index="${i}">
                <input type="hidden" name="speakers_payload[${i}][type]" value="outsource">
                <div class="row g-2">
                <div class="col-md-3">
                    <input class="form-control" name="speakers_payload[${i}][name]" placeholder="Nama" required>
                </div>
                <div class="col-md-3">
                    <input class="form-control" name="speakers_payload[${i}][institution]" placeholder="Instansi" required>
                </div>
                <div class="col-md-3">
                    <input class="form-control" name="speakers_payload[${i}][title]" placeholder="Jabatan" required>
                </div>
                <div class="col-md-3">
                    <input class="form-control" type="email" name="speakers_payload[${i}][email]" placeholder="Email" required>
                </div>
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
                processResults:(data)=>{
                return {
                    results: $.map(data, function(item){
                    return {
                        id: item.employee_id,
                        text: (item.employee_id ?? '-') + ' - ' + (item.name ?? '-') + (item.company_name ? ' -- '+item.company_name : '')
                    };
                    })
                };
                },
                cache:true
            }
            });
        }

        $('#btnAddSpeaker').on('click', function(){
            const i = idx++;
            $('#ls-speakers-wrap').append(rowTplEmployee(i));
            initSelect2($('#ls_emp_'+i));
        });

        $(document).on('click', '.btnToOutsource', function(){
            const i = $(this).data('index');
            const $row = $(`.speaker-row[data-index="${i}"]`);
            $row.replaceWith(rowTplOutsource(i));
        });

        $(document).on('click', '.btnToEmployee', function(){
            const i = $(this).data('index');
            const $row = $(`.speaker-row[data-index="${i}"]`);
            $row.replaceWith(rowTplEmployee(i));
            initSelect2($('#ls_emp_'+i));
        });

        $(document).on('click', '.btnRemoveRow', function(){
            const i = $(this).data('index');
            $(`.speaker-row[data-index="${i}"]`).remove();
        });

        const oldPayload = @json(old('speakers_payload', []));
        if (Object.keys(oldPayload).length){
            Object.keys(oldPayload).forEach(function(k){
            const item = oldPayload[k];
            if (item.type === 'outsource'){
                $('#ls-speakers-wrap').append(rowTplOutsource(k));
                $(`input[name="speakers_payload[${k}][name]"]`).val(item.name || '');
                $(`input[name="speakers_payload[${k}][institution]"]`).val(item.institution || '');
                $(`input[name="speakers_payload[${k}][title]"]`).val(item.title || '');
                $(`input[name="speakers_payload[${k}][email]"]`).val(item.email || '');
            } else {
                $('#ls-speakers-wrap').append(rowTplEmployee(k));
                initSelect2($('#ls_emp_'+k));
                const $sel = $('#ls_emp_'+k);
                if (item.employee_id){
                const opt = new Option(item.employee_id, item.employee_id, true, true);
                $sel.append(opt).trigger('change');
                }
            }
            idx = Math.max(idx, parseInt(k,10)+1);
            });
        } else {
            $('#btnAddSpeaker').trigger('click');
        }

        initSelect2($('#ls-opening-select'));

        @if(old('opening_speech_employee_id'))
            (function(){
            const val = @json(old('opening_speech_employee_id'));
            const opt = new Option(val, val, true, true);
            $('#ls-opening-select').append(opt).trigger('change');
            })();
        @endif

        $('#org-select').select2({
            width: '100%',
            placeholder: 'Pilih Departemen/Unit…',
            allowClear: true,
            ajax: {
            headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: "{{ route('query.autocomplete') }}",
            dataType: 'json',
            delay: 250,
            data: params => ({
                type: 'org',                 
                query: params.term || ''     
            }),
            processResults: data => ({ results: data.results || [] }),
            cache: true
            }
        });

        @php
            $preset = old('requesting_department', $currentOrgLabel ?? '');
        @endphp
        @if(!empty($preset))
            (function(){
            const val = @json($preset);
            const opt = new Option(val, val, true, true);
            $('#org-select').append(opt).trigger('change');
            })();
        @endif

        })();
    </script>
@endpush

