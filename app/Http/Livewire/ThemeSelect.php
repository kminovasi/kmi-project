<?php

namespace App\Http\Livewire;

use App\Models\Theme;
use Livewire\Component;

class ThemeSelect extends Component
{
    public $themes = [];

    public function mount () {
        $this->themes = Theme::select('id', 'theme_name')->get();
    }
    public function render()
    {
        return view('livewire.theme-select');
    }
}
