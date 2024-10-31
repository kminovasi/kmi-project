<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Switches extends Component
{
    public $status = true;

    public function render()
    {
        return view('livewire.switches');
    }
}
