<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
use App\Models\Paper;
use DB;
use Illuminate\View\Component;

class TotalPotentialBenefitByOrganizationChart extends Component
{
    public $organizationUnit;
    public $chartData;
    /**
     * Create a new component instance.
     *
     * @return void
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

        // Ambil data total financial benefit
        $this->chartData = Paper::select(
            DB::raw("COALESCE(user_hierarchy_histories.{$this->organizationUnit}, users.{$this->organizationUnit}) as organization_unit"),
            DB::raw('EXTRACT(YEAR FROM papers.created_at) as year'),
            DB::raw('SUM(papers.potential_benefit) as total_potential_benefit')
        )
            ->join('teams', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_members', function ($join) {
                $join->on('teams.id', '=', 'pvt_members.team_id')
                    ->where('pvt_members.status', 'leader'); // Hanya ambil leader
            })
            ->join('users', 'pvt_members.employee_id', '=', 'users.employee_id')
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
                return $data->keyBy('year')->map(fn($item) => $item->total_potential_benefit);
            });
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.total-potential-benefit-by-organization-chart', [
            'chartData' => $this->chartData,
            'organizationUnit' => $this->organizationUnit,
        ]);
    }
}
