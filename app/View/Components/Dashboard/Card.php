<?php

namespace App\View\Components\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Log;

class Card extends Component
{
    public  $ideaBox;
    public  $detailIdeaBoxIdea;
    public  $totalInnovators;
    public  $totalInnovatorsMale;
    public  $totalInnovatorsFemale;
    public $totalInnovatoresOutsource;
    public $totalActiveEvents;
    public $implemented;
    public $totalImplementedInnovations;
    public $totalIdeaBoxInnovations;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $ideaBox = null,
        $implemented,
        $totalInnovators = null,
        $totalInnovatorsMale = null,
        $totalInnovatorsFemale = null,
        $totalInnovatoresOutsource = null,
        $totalActiveEvents = null,
        $totalImplementedInnovations = null,
        $totalIdeaBoxInnovations = null
    ) {
        $this->ideaBox = $ideaBox;
        $this->implemented = $implemented;
        $this->totalInnovators = $totalInnovators;
        $this->totalInnovatorsMale = $totalInnovatorsMale;
        $this->totalInnovatorsFemale = $totalInnovatorsFemale;
        $this->totalInnovatoresOutsource = $totalInnovatoresOutsource;
        $this->totalActiveEvents = $totalActiveEvents;
        $this->totalImplementedInnovations = $totalImplementedInnovations;
        $this->totalIdeaBoxInnovations = $totalIdeaBoxInnovations;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $isSuperadmin = Auth::user()->role === 'Superadmin';
        $isAdmin = Auth::user()->role === 'Admin';
        return view('components.dashboard.card', [
            'isSuperadmin' => $isSuperadmin,
            'isAdmin' => $isAdmin,
        ]);
    }
}