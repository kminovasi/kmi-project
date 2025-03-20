<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class EventSelect extends Component
{
    public $selectedEvent = '';

    public function updatedSelectedEvent()
    {
        $this->emit('eventSelected', $this->selectedEvent);
    }

    public function render()
    {
        $user = Auth::user();

        $query = Event::with('company')
            ->orderBy('year', 'desc');

        if ($user->role !== 'Superadmin') {
            $query->whereHas('companies', function ($q) use ($user) {
                $q->where('company_code', $user->company_code);
            });
        }

        $events = $query->get();
        return view('livewire.event-select', compact('events'));
    }
}