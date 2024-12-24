@extends('layouts.app')
@section('title', 'Create Timeline')
@section('content')
<x-header-content title="Create Timeline" />
<div class="container card p-3">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('timeline.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="event_id" class="form-label">Event</label>
            <select id="event_id" name="event_id" class="form-select select2">
                <option value="">Select an event</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" data-start="{{ $event->date_start }}" data-end="{{ $event->date_end }}">{{ $event->event_name }}</option>
                @endforeach
            </select>
            <small id="event-dates" class="form-text text-muted"></small>
        </div>
        <div class="mb-3">
            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
            <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
            <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="judul_kegiatan" class="form-label">Judul Kegiatan</label>
            <input type="text" id="judul_kegiatan" name="judul_kegiatan" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
</div>

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select an event',
            allowClear: true
        });

        $('#event_id').on('change', function() {
            var selectedOption = $(this).find('option:selected');
            var startDate = selectedOption.data('start');
            var endDate = selectedOption.data('end');
            if (startDate && endDate) {
                $('#event-dates').text('Event dates: ' + startDate + ' to ' + endDate);
            } else {
                $('#event-dates').text('');
            }
        });
    });
</script>
@endpush
@endsection
