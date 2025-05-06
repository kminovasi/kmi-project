<?php

// app/View/Components/Patent/PatentTable.php

namespace App\View\Components\Patent;

use Illuminate\View\Component;

class PatentBodyTable extends Component
{
    public $patentData;

    public function __construct($patentData)
    {
        $this->patentData = $patentData;
    }

    public function render()
    {
        return view('components.patent.patent-body-table');
    }
}