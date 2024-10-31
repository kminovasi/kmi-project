<?php

namespace App\View\Components\DetailCompanyChart;

use App\Models\Company;
use App\Models\Paper;
use DB;
use Illuminate\View\Component;

class FinancialBenefitOrganization extends Component
{
    public $companyId;
    public $financialBenefitsByDirectorate;
    public $organizationUnit;
    public $year;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($companyId, $organizationUnit = null, $year)
    {
        $this->companyId = $companyId;
        $this->year = $year;
        $this->organizationUnit = $organizationUnit === null ? 'directorate_name' : $organizationUnit;
        $this->financialBenefitsByDirectorate = $this->getFinancialBenefitsByDirectorate();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.detail-company-chart.financial-benefit-organization', [
            'financialBenefitsByDirectorate' => $this->financialBenefitsByDirectorate,
        ]);
    }

    private function getFinancialBenefitsByDirectorate()
    {
        $company = Company::findOrFail($this->companyId);

        return Paper::select('users.' . $this->organizationUnit, DB::raw('SUM(papers.financial) as total_potential_benefit'))
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
            ->join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
            ->where('teams.company_code', $company->company_code)
            ->whereYear('teams.created_at', $this->year)
            ->groupBy('users.' . $this->organizationUnit)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item[$this->organizationUnit] => $item['total_potential_benefit']];
            });
    }
}
