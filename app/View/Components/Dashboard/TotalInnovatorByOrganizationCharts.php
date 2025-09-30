<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class TotalInnovatorByOrganizationCharts extends Component
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

        $company = Company::findOrFail($companyId);
        $this->company_name = $company->company_name;
        $companyCode = (string) $company->company_code;

        $companyCodes = in_array($companyCode, ['2000', '7000'], true)
            ? ['2000', '7000']
            : [$companyCode];

        $allowedUnits = [
            'GHoPO Tuban',
            'Supply Chain Directorate',
            'Operation Directorate',
            'Human Capital & General Affair Directorate',
            'Finance & Portfolio Mgmt Directorate',
            'Business & Marketing Directorate',
            'President Directorate',
        ];
        $allowedLower   = array_map(fn($s) => mb_strtolower($s), $allowedUnits);
        $allowedInLower = "'" . implode("','", array_map('addslashes', $allowedLower)) . "'";

        $col  = "pm.{$this->organizationUnit}";
        $norm = "LOWER(TRIM({$col}))";

        $selectOrgUnitForPermanent = $this->organizationUnit === 'directorate_name'
            ? "
              CASE
                WHEN {$col} IS NULL OR TRIM({$col}) = '' THEN 'Lainnya'
                WHEN t.company_code IN ('2000','7000') THEN
                  CASE
                    WHEN {$norm} IN ({$allowedInLower}) THEN
                      CASE
                        WHEN {$norm} IN ('gopo tuban','ghopo tuban') THEN 'GHoPO Tuban'
                        WHEN {$norm} = 'supply chain directorate' THEN 'Supply Chain Directorate'
                        WHEN {$norm} = 'operation directorate' THEN 'Operation Directorate'
                        WHEN {$norm} = 'human capital & general affair directorate' THEN 'Human Capital & General Affair Directorate'
                        WHEN {$norm} IN ('finance & portfolio mgmt directorate','finance & portfolio management directorate') THEN 'Finance & Portfolio Mgmt Directorate'
                        WHEN {$norm} = 'business & marketing directorate' THEN 'Business & Marketing Directorate'
                        WHEN {$norm} = 'president directorate' THEN 'President Directorate'
                        ELSE 'Lainnya'
                      END
                    ELSE 'Lainnya'
                  END
                ELSE
                  COALESCE(NULLIF(TRIM({$col}), ''), 'Lainnya')
              END
              "
            : "COALESCE(NULLIF(TRIM({$col}), ''), 'Lainnya')";

        $permanentQuery = DB::table('teams as t')
            ->join('pvt_members as pm', function ($join) {
                $join->on('pm.team_id', '=', 't.id')
                     ->whereRaw("LOWER(TRIM(pm.status)) <> 'gm'");
            })
            ->join('pvt_event_teams as pet', 'pet.team_id', '=', 't.id')
            ->join('events as e', 'e.id', '=', 'pet.event_id')
            ->join('papers as p', function ($join) {
                $join->on('p.team_id', '=', 't.id')
                     ->where('p.status', 'accepted by innovation admin');
            })
            ->whereIn('t.company_code', $companyCodes)
            ->where('e.year', $this->year)
            ->selectRaw("
                {$selectOrgUnitForPermanent} AS organization_unit,
                e.year AS year,
                CONCAT('perm:', pm.employee_id, '-', t.id) AS unique_person_key
            ");

        $outsourcingQuery = DB::table('ph2_members as ph2')
            ->join('teams as t2', 'ph2.team_id', '=', 't2.id')
            ->join('pvt_event_teams as pet2', 'pet2.team_id', '=', 't2.id')
            ->join('events as e2', 'e2.id', '=', 'pet2.event_id')
            ->join('papers as p2', function ($join) {
                $join->on('p2.team_id', '=', 't2.id')
                     ->where('p2.status', 'accepted by innovation admin');
            })
            ->whereIn('t2.company_code', $companyCodes)
            ->where('e2.year', $this->year)
            ->selectRaw("
                'Lainnya' AS organization_unit,
                e2.year AS year,
                CONCAT('ph2:', ph2.name, '-', t2.id) AS unique_person_key
            ");

        $combined = $permanentQuery->unionAll($outsourcingQuery);

        $this->chartData = DB::table(DB::raw("({$combined->toSql()}) as combined"))
            ->mergeBindings($combined)
            ->select(
                'organization_unit',
                'year',
                DB::raw('COUNT(DISTINCT unique_person_key) AS total_innovators')
            )
            ->groupBy('organization_unit', 'year')
            ->orderByDesc('total_innovators') 
            ->get()
            ->groupBy('organization_unit')    
            ->map(fn($rows) => $rows->pluck('total_innovators', 'year'));
        
        }

    public function render()
    {
        return view('components.dashboard.total-innovator-by-organization-charts', [
            'chartData' => $this->chartData,
            'organizationUnit' => $this->organizationUnit,
            'company_name' => $this->company_name
        ]);
    }
}
