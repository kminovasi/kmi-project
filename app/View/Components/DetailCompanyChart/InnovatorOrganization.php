<?php

namespace App\View\Components\DetailCompanyChart;

use App\Models\Company;
use App\Models\User;
use DB;
use Illuminate\View\Component;

class InnovatorOrganization extends Component
{
    public $companyId;
    public $innovatorsByDirectorate;
    public $organizationUnit;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($companyId = null, $organizationUnit = null)
    {
        $this->companyId = $companyId;
        $this->organizationUnit = $organizationUnit === null ? 'directorate_name' : $organizationUnit;
        $this->innovatorsByDirectorate = $this->getInnovatorsByDirectorate();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.detail-company-chart.innovator-per-organization-unit', [
            'innovatorsByDirectorate' => $this->innovatorsByDirectorate
        ]);
    }

    private function getInnovatorsByDirectorate()
    {
        $company = Company::findOrFail($this->companyId);
        return User::select($this->organizationUnit, DB::raw('COUNT(DISTINCT users.id) as total_innovators'))
            ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->where('teams.company_code', $company->company_code)
            ->where('pvt_members.status', 'member')
            ->groupBy($this->organizationUnit)
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item[$this->organizationUnit] => $item['total_innovators']];
            });
    }
}
