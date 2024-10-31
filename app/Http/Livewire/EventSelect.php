<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Livewire\Component;

class EventSelect extends Component
{
    public $events = [];

    public function mount()
    {
        $this->events = Event::all();
    }
    public function render()
    {
        return view('livewire.event-select');
    }
}
