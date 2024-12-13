<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;

class Header extends Component
{
    public $year;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($year)
    {
        $this->year = $year;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.header');
    }
}
