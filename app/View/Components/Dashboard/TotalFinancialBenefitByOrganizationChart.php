<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
use Illuminate\View\Component;
use App\Models\Paper;
use Illuminate\Support\Facades\DB;
use Log;

class TotalFinancialBenefitByOrganizationChart extends Component
{
    public $organizationUnit;
    public $chartData;
    public $company_name;
    public $year;

    public function __construct($organizationUnit = null, $companyId, $year)
    {
        $this->organizationUnit = $organizationUnit ?? 'directorate_name';
        $this->year = $year;

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

        $company       = Company::findOrFail($companyId);
        $companyCode   = $company->company_code;
        $this->company_name = $company->company_name;

        $filteredCodes = in_array((int)$companyCode, [2000, 7000]) ? [2000, 7000] : [(int)$companyCode];

        $base = DB::table('teams as t')
            ->join('papers as p', function ($j) {
                $j->on('p.team_id', '=', 't.id')
                  ->whereRaw("TRIM(LOWER(p.status)) = 'accepted by innovation admin'");
            })
            ->whereIn('t.company_code', $filteredCodes)
            ->selectRaw('t.id AS team_id, CAST(SUM(COALESCE(p.financial,0)) AS SIGNED) AS team_financial')
            ->groupBy('t.id');

        $finishCount = DB::table('pvt_event_teams as pet')
            ->join('events as events', 'events.id', '=', 'pet.event_id')
            ->join('teams as t', 't.id', '=', 'pet.team_id')
            ->leftJoin('pvt_members as pm', function ($j) {
                $j->on('pm.team_id', '=', 't.id')->where('pm.status', 'leader');
            })
            ->leftJoin('users as users', 'users.employee_id', '=', 'pm.employee_id')
            ->leftJoin('user_hierarchy_histories as uhh', function ($j) {
                $j->on('uhh.user_id', '=', 'users.id')
                  ->whereRaw('(COALESCE(events.date_start, events.created_at)) >= COALESCE(uhh.effective_start_date, COALESCE(events.date_start, events.created_at))')
                  ->whereRaw('(COALESCE(events.date_start, events.created_at)) <= COALESCE(uhh.effective_end_date, COALESCE(events.date_start, events.created_at))');
            })
            ->where('events.status', 'finish')
            ->where('events.year', $this->year)
            ->whereIn('t.company_code', $filteredCodes)
            ->selectRaw("
                pet.team_id,
                COALESCE(uhh.{$this->organizationUnit}, users.{$this->organizationUnit}, 'Lainnya') AS organization_unit,
                COUNT(DISTINCT pet.event_id) AS finish_events,
                events.year AS year
            ")
            ->groupBy('pet.team_id', 'organization_unit', 'events.year');

        $data = DB::query()
            ->fromSub($base, 'tp')
            ->joinSub($finishCount, 'fc', 'fc.team_id', '=', 'tp.team_id')
            ->selectRaw('
                fc.organization_unit,
                fc.year,
                CAST(SUM(COALESCE(tp.team_financial,0) * COALESCE(fc.finish_events,0)) AS SIGNED) AS total_financial
            ')
            ->groupBy('fc.organization_unit', 'fc.year')
            ->orderBy('fc.organization_unit')
            ->get()
            ->map(function ($r) {
                $r->year = (int) $r->year;
                $r->total_financial = (int) $r->total_financial;
                return $r;
            })

            ->filter(fn ($r) => $r->total_financial > 0)
            ->values();

        // $grandTotalRaw = (int) $data->sum('total_financial');
        // $totalPerUnit  = $data->groupBy('organization_unit')->map(fn($g) => (int) $g->sum('total_financial'));
        // $totalPerYear  = $data->groupBy('year')->map(fn($g) => (int) $g->sum('total_financial'));

        // Log::debug('TFB OrgChart RAW totals', [
        //     'company_name'   => $this->company_name,
        //     'company_code'   => $companyCode,
        //     'filtered_codes' => $filteredCodes,
        //     'year'           => $this->year,
        //     'organizationUnit' => $this->organizationUnit,
        //     'rows'           => $data->count(),
        //     'grand_total'    => $grandTotalRaw,
        //     'grand_total_fmt'=> 'Rp '.number_format($grandTotalRaw, 0, ',', '.'),
        //     'per_unit'       => $totalPerUnit,
        //     'per_year'       => $totalPerYear,
        // ]);

        $this->chartData = $data
            ->groupBy('organization_unit')
            ->map(fn ($rows) => $rows->keyBy('year')->map(fn ($r) => (string) $r->total_financial));
    }
    
    public function render()
    {
        return view('components.dashboard.total-financial-benefit-by-organization-chart', [
            'chartData' => $this->chartData,
            'organizationUnit' => $this->organizationUnit,
            'company_name' => $this->company_name,
        ]);
    }
}
