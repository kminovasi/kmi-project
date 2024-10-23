<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;

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
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        $breakthroughInnovation = null,
        $detailBreakthroughInnovationManagement = null,
        $incrementalInnovation = null,
        $detailIncrementalInnovationGKMOffice = null,
        $detailIncrementalInnovationPKMOffice = null,
        $detailIncrementalInnovationSSPlant = null, // Nilai default null
        $ideaBox = null,
        $detailIdeaBoxIdea = null,
        $detailBreakthroughInnovationPBB = null,
        $detailBreakthroughInnovationTPP = null,
        $detailIncrementalInnovationPKMPlant = null,
        $totalInnovators = null,
        $totalInnovatorsMale = null,
        $totalInnovatorsFemale = null,
    ) {
        $this->breakthroughInnovation = $breakthroughInnovation;
        $this->detailBreakthroughInnovationManagement = $detailBreakthroughInnovationManagement;
        $this->incrementalInnovation = $incrementalInnovation;
        $this->detailIncrementalInnovationGKMOffice = $detailIncrementalInnovationGKMOffice;
        $this->detailIncrementalInnovationPKMOffice = $detailIncrementalInnovationPKMOffice;
        $this->detailIncrementalInnovationSSPlant = $detailIncrementalInnovationSSPlant;
        $this->ideaBox = $ideaBox;
        $this->detailIdeaBoxIdea = $detailIdeaBoxIdea;
        $this->detailBreakthroughInnovationPBB = $detailBreakthroughInnovationPBB;
        $this->detailBreakthroughInnovationTPP = $detailBreakthroughInnovationTPP;
        $this->detailIncrementalInnovationPKMPlant = $detailIncrementalInnovationPKMPlant;
        $this->totalInnovators = $totalInnovators;
        $this->totalInnovatorsMale = $totalInnovatorsMale;
        $this->totalInnovatorsFemale = $totalInnovatorsFemale;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.card');
    }
}
