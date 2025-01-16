<?php

namespace App\View\Components\Dashboard;

use App\Models\CustomBenefitFinancial;
use Illuminate\View\Component;

class TotalNonFinancialBenefitCard extends Component
{
    public $data;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($isSuperadmin, $userCompanyCode)
    {
        $this->data = CustomBenefitFinancial::withCount('papers') // Count related papers
        ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.total-non-financial-benefit-card', ['data' => $this->data]);
    }
}
