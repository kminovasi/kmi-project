<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
use Illuminate\View\Component;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class TotalTeamByOrganizationChart extends Component
{
    public $organizationUnit;
    public $chartData;
    public $company_name;
    public $year;

    /**
     * Create a new component instance.
     *
     * @param string|null $organizationUnit
     */
    public function __construct($organizationUnit = null, $companyId, $year)
    {
        $this->organizationUnit = $organizationUnit ?? 'directorate_name';
        $this->year = $year ?? now()->year;

        $validOrganizationUnits = [
            'directorate_name',
            'group_function_name',
            'department_name',
            'unit_name',
            'section_name',
            'sub_section_of',
        ];

        if (!in_array($this->organizationUnit, $validOrganizationUnits)) {
            throw new \InvalidArgumentException("Invalid organization unit: {$this->organizationUnit}");
        }

        $company = Company::findOrFail($companyId);
        $companyCode = $company->company_code;
        $this->company_name = $company->company_name;
        
        if(in_array($companyCode, [2000, 7000])){
            $filterCompany = [2000, 7000];
        } else {
            $filterCompany = [$companyCode];
        }

        $this->chartData = Team::selectRaw("
        COALESCE(NULLIF(NULLIF(TRIM(pvt_members.{$this->organizationUnit}), ''), '-'), 'Lainnya') AS organization_unit,
        CAST(events.year AS UNSIGNED) AS year,
        COUNT(DISTINCT teams.id) AS total_teams
        ")
        ->join('pvt_members', function ($join) {
            $join->on('teams.id', '=', 'pvt_members.team_id')
                 ->where('pvt_members.status', 'leader');
        })
        ->join('papers', function ($join) {
            $join->on('teams.id', '=', 'papers.team_id')
                 ->where('papers.status', 'accepted by innovation admin');
        })
        ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
        ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
        ->whereIn('teams.company_code', $filterCompany)
        ->whereYear('events.year', $this->year)
        ->groupBy('organization_unit', 'year')
        ->orderBy('total_teams', 'DESC')
        ->get()
        ->groupBy('organization_unit')
        ->map(fn ($items) => $items->keyBy('year')->map->total_teams);
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.total-team-by-organization-chart', [
            'chartData' => $this->chartData,
            'organizationUnit' => $this->organizationUnit,
            'company_name' => $this->company_name
        ]);
    }
}