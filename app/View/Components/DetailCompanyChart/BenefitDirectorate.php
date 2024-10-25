<?php

namespace App\View\Components\DetailCompanyChart;

use App\Models\Company;
use App\Models\Paper;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;

class BenefitDirectorate extends Component
{
    public $companyId;
    public $potentialBenefitsByDirectorate;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($companyId)
    {
        $this->companyId = $companyId;
        $this->potentialBenefitsByDirectorate = $this->getPotentialBenefitsByDirectorate();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.detail-company-chart.benefit-directorate', [
            'potentialBenefitsByDirectorate' => $this->potentialBenefitsByDirectorate,
        ]);
    }

    private function getPotentialBenefitsByDirectorate()
    {
        $company = Company::findOrFail($this->companyId);

        return Paper::select('users.directorate_name', DB::raw('SUM(papers.potential_benefit) as total_potential_benefit'))
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_members', 'teams.id', '=', 'pvt_members.team_id')
            ->join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
            ->where('teams.company_code', $company->company_code)
            ->groupBy('users.directorate_name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['directorate_name'] => $item['total_potential_benefit']];
            });
    }
}
