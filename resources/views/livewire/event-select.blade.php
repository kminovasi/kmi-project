<div>
    <select name="event" id="eventSelect" class="form-select form-select-sm">
        <option value="">-- Pilih Event --</option>
        @foreach ($events as $event)
        <option value="{{ $event->id }}" {{ request('event')==$event->id ? 'selected' : '' }}>
            {{ $event->event_name }} {{ $event->year }}
        </option>
        @endforeach
    </select>
</div>
