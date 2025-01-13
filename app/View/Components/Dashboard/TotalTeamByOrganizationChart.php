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

    /**
     * Create a new component instance.
     *
     * @param string|null $organizationUnit
     */
    public function __construct($organizationUnit = null, $companyId)
    {
        // Tetapkan nilai default jika $organizationUnit null
        $this->organizationUnit = $organizationUnit ?? 'directorate_name';

        // Validasi apakah organizationUnit adalah kolom yang valid
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
        $currentYear = now()->year;
        $this->company_name = $company->company_name;

        // Ambil data 4 tahun terakhir
        $this->chartData = Team::select(
            DB::raw("COALESCE(user_hierarchy_histories.{$this->organizationUnit}, users.{$this->organizationUnit}) as organization_unit"),
            DB::raw('EXTRACT(YEAR FROM papers.created_at) as year'),
            DB::raw('COUNT(teams.id) as total_teams')
        )
            ->join('pvt_members', function ($join) {
                $join->on('teams.id', '=', 'pvt_members.team_id')
                    ->where('pvt_members.status', 'leader');
            })
            ->join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
            ->join('papers', function ($join) {
                $join->on('teams.id', '=', 'papers.team_id')
                    ->where('papers.status', 'accepted by innovation admin');
            })
            ->leftJoin('user_hierarchy_histories', function ($join) {
                $join->on('user_hierarchy_histories.user_id', '=', 'users.id')
                    ->whereRaw('papers.created_at >= COALESCE(user_hierarchy_histories.effective_start_date, papers.created_at)')
                    ->whereRaw('papers.created_at <= COALESCE(user_hierarchy_histories.effective_end_date, papers.created_at)');
            })
            ->where('teams.company_code', $companyCode)
            ->whereBetween(DB::raw('EXTRACT(YEAR FROM papers.created_at)'), [$currentYear - 3, $currentYear])
            ->groupBy(DB::raw("COALESCE(user_hierarchy_histories.{$this->organizationUnit}, users.{$this->organizationUnit})"), DB::raw('EXTRACT(YEAR FROM papers.created_at)'))
            ->orderBy(DB::raw("COALESCE(user_hierarchy_histories.{$this->organizationUnit}, users.{$this->organizationUnit})"))
            ->get()
            ->groupBy('organization_unit')
            ->map(function ($data) {
                return $data->keyBy('year')->map(fn($item) => $item->total_teams);
            });
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
