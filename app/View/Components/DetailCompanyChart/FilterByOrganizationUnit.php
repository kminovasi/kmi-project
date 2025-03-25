<?php

namespace App\View\Components\DetailCompanyChart;

use Illuminate\View\Component;
use Log;

class FilterByOrganizationUnit extends Component
{
    public $companyId;
    public $availableYears;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($organizationUnit = null, $companyId = null, $availableYears)
    {
        $this->companyId = $companyId;
        $this->availableYears = $availableYears;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.detail-company-chart.filter-by-organization-unit', [
            'companyId' => $this->companyId,
            'availableYears' => $this->availableYears
        ]);
    }
}