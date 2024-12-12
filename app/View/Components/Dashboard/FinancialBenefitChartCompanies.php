<?php

namespace App\View\Components\Dashboard;

use App\Http\Controllers\DashboardController;
use Illuminate\View\Component;

class FinancialBenefitChartCompanies extends Component
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
        $financialData = app(DashboardController::class)->getFinancialBenefitsByCompany()->getData(true);

        return view('components.dashboard.financial-benefit-chart-companies', [
            'financialData' => $financialData
        ]);
    }
}
