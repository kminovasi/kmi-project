<?php

namespace App\Http\Livewire;

use App\Models\Company;
use Livewire\Component;

class CompanySelect extends Component
{
    public $selectedCompany = '';

    public function updatedSelectedCompany()
    {
        $this->emit('companySelected', $this->selectedCompany);
    }

    public function render()
    {
        $companies = Company::all();
        return view('livewire.company-select', compact('companies'));
    }
}
