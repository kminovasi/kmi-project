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
    $result = app(\App\Http\Controllers\DashboardController::class)
        ->getFinancialBenefitsByCompany(); // <- sudah array

    $financialData = is_array($result) ? $result : (array) $result;

    return view('components.dashboard.financial-benefit-chart-companies', [
        'financialData' => $financialData,
    ]);
}

}
