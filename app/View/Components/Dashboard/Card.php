<?php

namespace App\View\Components\Dashboard;

use Auth;
use Illuminate\View\Component;
use Log;

class Card extends Component
{
    public $breakthroughInnovation;
    public  $detailBreakthroughInnovationManagement;
    public  $incrementalInnovation;
    public  $detailIncrementalInnovationPKMPlant;
    public  $detailIncrementalInnovationGKMOffice;
    public  $detailIncrementalInnovationPKMOffice;
    public $detailIncrementalInnovationSSPlant = null;
    public  $ideaBox;
    public  $detailIdeaBoxIdea;
    public  $detailBreakthroughInnovationPBB;
    public  $detailBreakthroughInnovationTPP;
    public  $detailIncrementalInnovationGKMPlant;
    public  $detailIncrementalInnovationSSOffice;
    public  $totalInnovators;
    public  $totalInnovatorsMale;
    public  $totalInnovatorsFemale;
    public $totalActiveEvents;
    public $categories;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $categories = null,
        $totalInnovators = null,
        $totalInnovatorsMale = null,
        $totalInnovatorsFemale = null,
        $totalActiveEvents = null,
    ) {
        $this->totalInnovators = $totalInnovators;
        $this->totalInnovatorsMale = $totalInnovatorsMale;
        $this->totalInnovatorsFemale = $totalInnovatorsFemale;
        $this->totalActiveEvents = $totalActiveEvents;
        $this->categories = $categories;
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
