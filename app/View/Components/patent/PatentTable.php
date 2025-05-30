<?php

namespace App\View\Components\patent;

use App\Models\Patent;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class PatentTable extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $patentData = Patent::with(['paper', 'employee', 'patenMaintenance'])
            ->visibleTo(Auth::user()) // Assuming visibleTo() scope is defined in the Patent model
            ->paginate(10);
        
        return view('components.patent.patent-table', compact(
            'patentData'
        ));
    }
}