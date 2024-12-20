<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Livewire\Component;

class TimelineEvent extends Component
{
    public $events;
    public function mount(): void {
        $this->events = Event::where('status', 'active')
            ->orderBy('date_start', 'asc')
            ->get();
    }

    public function render()
    {
        return view('livewire.timeline-event', [
            'events' => $this->events
        ]);
    }
}
