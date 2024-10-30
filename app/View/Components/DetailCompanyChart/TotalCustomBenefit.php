<?php

namespace App\View\Components\DetailCompanyChart;

use App\Models\Company;
use App\Models\CustomBenefitFinancial;
use App\Models\PvtCustomBenefit;
use Illuminate\View\Component;

class TotalCustomBenefit extends Component
{
    public $companyId;
    public $benefits;
    public $totals;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
        $this->loadBenefits();
    }

    private function loadBenefits()
    {
        $company = Company::find($this->companyId);

        // Mengambil semua custom benefit untuk perusahaan ini
        $this->benefits = CustomBenefitFinancial::where('company_code', $company->company_code)->get();

        // Menghitung total untuk setiap benefit
        $this->totals = [];
        foreach ($this->benefits as $benefit) {
            $total = PvtCustomBenefit::where('custom_benefit_financial_id', $benefit->id)
                ->sum('value');

            $this->totals[$benefit->id] = number_format($total, 0, ',', '.');
        }
    }

    public function render()
    {
        return view('components.detail-company-chart.total-custom-benefit');
    }
}
