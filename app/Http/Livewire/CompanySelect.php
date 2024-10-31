<?php

namespace App\Http\Livewire;

use App\Models\Company;
use Livewire\Component;

class CompanySelect extends Component
{
    public $companies = [];

    public function mount()
    {
        $this->companies = Company::all();
    }
    public function render()
    {
        return view('livewire.company-select');
    }
}
