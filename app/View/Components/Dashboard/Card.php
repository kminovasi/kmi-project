<?php

namespace App\View\Components\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Log;
use Illuminate\Support\Collection; 

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
     public $metodologi; 
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $ideaBox = null,
        $implemented,
        $metodologi = [],
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
         $this->metodologi   = $this->toArraySafe($metodologi);
        $this->totalInnovators = $totalInnovators;
        $this->totalInnovatorsMale = $totalInnovatorsMale;
        $this->totalInnovatorsFemale = $totalInnovatorsFemale;
        $this->totalInnovatoresOutsource = $totalInnovatoresOutsource;
        $this->totalActiveEvents = $totalActiveEvents;
        $this->totalImplementedInnovations = $totalImplementedInnovations;
        $this->totalIdeaBoxInnovations = $totalIdeaBoxInnovations;
    }

    private function toArraySafe($value): array
    {
        if (is_array($value)) return $value;
        if ($value instanceof Collection) return $value->toArray();
        return $value ? (array) $value : [];
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