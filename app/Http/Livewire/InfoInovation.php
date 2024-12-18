<?php

namespace App\Http\Livewire;

use Livewire\Component;

class InfoInovation extends Component
{
    protected $totalInovations;
    protected $totalInovator;
    protected $totalInovationImplemented;

    public function mount()
    {
        $this->totalInovations = \App\Models\Paper::count();
        $this->totalInovator = \App\Models\PvtMember::distinct('employee_id')->count('employee_id');
        // $this->totalInovationImplemented = \App\Models\PvtEventTeam::distinct('event_id')->count('event_id');
    }
    public function render()
    {
        return view('livewire.info-inovation', [
            'totalInovations' => $this->totalInovations,
            'totalInovator' => $this->totalInovator,
            'totalInovationImplemented' => $this->totalInovationImplemented,
        ]);
    }
}
