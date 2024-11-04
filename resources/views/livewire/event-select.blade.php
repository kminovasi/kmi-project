<div>
    <select name="event" wire:model="selectedEvent" class="form-select form-select-sm">
        <option value="">-- Pilih Event --</option>
        @foreach ($events as $event)
        <option value="{{ $event->id }}">
            {{ $event->event_name }} {{ $event->year }}
        </option>
        @endforeach
    </select>
</div>
