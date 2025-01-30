<?php

namespace App\View\Components\Assessment;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ShowFullPaperButton extends Component
{
    public $fullPaperPath;
    public $fullPaperUpdatedAt;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($fullPaperPath = null, $fullPaperUpdatedAt = null)
    {
        $this->fullPaperPath = $fullPaperPath ? Storage::url(mb_substr($fullPaperPath, 3)) : null;
        $this->fullPaperUpdatedAt = $fullPaperUpdatedAt;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.assessment.show-full-paper-button', [
            'fullPaperPath' => $this->fullPaperPath,
            'fullPaperUpdatedAt' => $this->fullPaperUpdatedAt
        ]);
    }
}
