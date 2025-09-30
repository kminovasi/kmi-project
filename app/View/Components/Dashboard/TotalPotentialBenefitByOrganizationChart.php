<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
use App\Models\Paper;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Component;
use Log;

class TotalPotentialBenefitByOrganizationChart extends Component
{
    public $organizationUnit;
    public $chartData;
    public $company_name;
    public $year;

    public function __construct($organizationUnit = null, $companyId, $year)
    {
        $this->organizationUnit = $organizationUnit ?? 'directorate_name';
        $this->year = $year;

        $valid = [
            'directorate_name',
            'group_function_name',
            'department_name',
            'unit_name',
            'section_name',
            'sub_section_of',
        ];
        if (!in_array($this->organizationUnit, $valid)) {
            throw new \InvalidArgumentException("Invalid organization unit: {$this->organizationUnit}");
        }

        $company     = Company::findOrFail($companyId);
        $companyCode = (int) $company->company_code;
        $this->company_name = $company->company_name;
        $filteredCodes = in_array($companyCode, [2000, 7000]) ? [2000, 7000] : [$companyCode];

        $base = DB::table('teams as t')
            ->join('papers as p', function ($j) {
                $j->on('p.team_id', '=', 't.id')
                  ->whereRaw("TRIM(LOWER(p.status)) = 'accepted by innovation admin'");
            })
            ->whereIn('t.company_code', $filteredCodes)
            ->selectRaw('t.id AS team_id, CAST(SUM(COALESCE(p.potential_benefit,0)) AS SIGNED) AS team_potential')
            ->groupBy('t.id');

        $eligible = DB::table('pvt_event_teams as pet')
            ->join('events as e', 'e.id', '=', 'pet.event_id')
            ->join('teams as t', 't.id', '=', 'pet.team_id')
            ->leftJoin('pvt_members as pm', function ($j) {
                $j->on('pm.team_id', '=', 't.id')->where('pm.status', 'leader');
            })
            ->leftJoin('users as u', 'u.employee_id', '=', 'pm.employee_id')
            ->leftJoin('user_hierarchy_histories as uhh', function ($j) {
                $j->on('uhh.user_id', '=', 'u.id')
                  ->whereRaw('(COALESCE(e.date_start, e.created_at)) >= COALESCE(uhh.effective_start_date, COALESCE(e.date_start, e.created_at))')
                  ->whereRaw('(COALESCE(e.date_start, e.created_at)) <= COALESCE(uhh.effective_end_date, COALESCE(e.date_start, e.created_at))');
            })
            ->where('e.status', 'finish')
            ->where('e.year', $this->year)
            ->whereIn('t.company_code', $filteredCodes)
            ->selectRaw("
                pet.team_id,
                e.year,
                MAX(COALESCE(uhh.{$this->organizationUnit}, u.{$this->organizationUnit}, 'Lainnya')) AS organization_unit
            ")
            ->groupBy('pet.team_id', 'e.year');

        $rows = DB::query()
            ->fromSub($base, 'tp')
            ->joinSub($eligible, 'elig', 'elig.team_id', '=', 'tp.team_id')
            ->selectRaw('elig.organization_unit, elig.year, CAST(SUM(tp.team_potential) AS SIGNED) AS total_potential')
            ->groupBy('elig.organization_unit', 'elig.year')
            ->orderBy('elig.organization_unit')
            ->get()
            ->map(function ($r) {
                $r->year = (int) $r->year;
                $r->total_potential = (int) $r->total_potential;
                return $r;
            })
            ->filter(fn ($r) => $r->total_potential > 0) 
            ->values();

        // $grandTotalRaw = (int) $rows->sum('total_potential');
        // $perUnitRaw    = $rows->groupBy('organization_unit')->map(fn($g) => (int) $g->sum('total_potential'));
        // $perYearRaw    = $rows->groupBy('year')->map(fn($g) => (int) $g->sum('total_potential'));
        // Log::debug('[TPB OrgChart RAW]', [
        //     'company_code' => $companyCode,
        //     'company_name' => $this->company_name,
        //     'year'         => $this->year,
        //     'units'        => $perUnitRaw->count(),
        //     'grand_total'  => $grandTotalRaw,
        //     'per_unit'     => $perUnitRaw,
        //     'per_year'     => $perYearRaw,
        // ]);

        $this->chartData = $rows
            ->groupBy('organization_unit')
            ->map(fn ($g) => $g->keyBy('year')->map(fn ($r) => (string) $r->total_potential));
    }

    public function render()
    {
        return view('components.dashboard.total-potential-benefit-by-organization-chart', [
            'chartData' => $this->chartData,
            'organizationUnit' => $this->organizationUnit,
            'company_name' => $this->company_name,
        ]);
    }
}
