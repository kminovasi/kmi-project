<?php

namespace App\View\Components\Dashboard;

use App\Models\CustomBenefitFinancial;
use App\Models\PvtCustomBenefit;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class TotalValueCustomBenefit extends Component
{
    public $benefitTotals;
    public $grandTotal;

    public function __construct()
    {
        $this->loadBenefitTotals();
    }

    private function loadBenefitTotals()
    {
        // Mengambil total value per custom benefit menggunakan join
        $this->benefitTotals = CustomBenefitFinancial::select(
            'custom_benefit_financials.id',
            'custom_benefit_financials.name_benefit',
            DB::raw('SUM(pvt_custom_benefits.value) as total_value')
        )
            ->leftJoin('pvt_custom_benefits', 'custom_benefit_financials.id', '=', 'pvt_custom_benefits.custom_benefit_financial_id')
            ->groupBy('custom_benefit_financials.id', 'custom_benefit_financials.name_benefit')
            ->get();

        // Menghitung grand total
        $this->grandTotal = $this->benefitTotals->sum('total_value');
    }

    public function render()
    {
        return view('components.dashboard.total-value-custom-benefit');
    }
}
