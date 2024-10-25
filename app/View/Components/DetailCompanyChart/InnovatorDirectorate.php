<?php

namespace App\View\Components\DetailCompanyChart;

use Illuminate\View\Component;
use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InnovatorDirectorate extends Component
{
    public $companyId;
    public $innovatorsByDirectorate;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($companyId = null)
    {
        $this->companyId = $companyId;
        $this->innovatorsByDirectorate = $this->getInnovatorsByDirectorate();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.detail-company-chart.innovator-directorate', [
            'innovatorsByDirectorate' => $this->innovatorsByDirectorate
        ]);
    }

    private function getInnovatorsByDirectorate()
    {
        $company = Company::findOrFail($this->companyId);

        return User::select('directorate_name', DB::raw('COUNT(DISTINCT users.id) as total_innovators'))
            ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->where('teams.company_code', $company->company_code)
            ->where('pvt_members.status', 'member')
            ->groupBy('directorate_name')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item['directorate_name'] => $item['total_innovators']];
            });
    }
}
