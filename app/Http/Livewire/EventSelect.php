<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventSelect extends Component
{
    public $selectedEvent = '';

    public function updatedSelectedEvent()
    {
        $this->emit('eventSelected', $this->selectedEvent);
    }

    public function render()
    {
        $events = Event::orderBy('year','desc')->get();
        return view('livewire.event-select', compact('events'));
    }
}
