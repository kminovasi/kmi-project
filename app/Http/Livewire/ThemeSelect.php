<?php

namespace App\Http\Livewire;

use App\Models\Theme;
use Livewire\Component;

class ThemeSelect extends Component
{

    public $selectedTheme = '';

    public function updatedSelectedTheme()
    {
        $this->emit('themeSelected', $this->selectedTheme);
    }

    public function render()
    {
        $themes = Theme::select('id', 'theme_name')->get();
        return view('livewire.theme-select', compact('themes'));
    }
}
