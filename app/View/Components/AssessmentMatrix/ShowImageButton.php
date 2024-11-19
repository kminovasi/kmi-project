<?php

namespace App\View\Components\AssessmentMatrix;

use App\Models\AssessmentMatrixImage;
use Illuminate\View\Component;
use Log;

class ShowImageButton extends Component
{
    public $assessmentMatrixImages;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->assessmentMatrixImages = AssessmentMatrixImage::all();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.assessment-matrix.show-image-button', [
            'assessmentMatrixImages' => $this->assessmentMatrixImages
        ]);
    }
}
