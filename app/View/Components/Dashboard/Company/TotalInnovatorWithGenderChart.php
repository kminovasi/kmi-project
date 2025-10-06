<?php
namespace App\View\Components\Dashboard\Company;

use App\Models\Company;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TotalInnovatorWithGenderChart extends Component
{
    public $chartData;
    public $companyName;

    /**
     * Create a new component instance.
     *
     * @param int $companyId
     * @return void
     */
    public function __construct($companyId)
    {
        $this->chartData = $this->fetchChartData($companyId);
    }

    /**
     * Fetch chart data for the company.
     *
     * @param int $companyId
     * @return array
     */
    private function fetchChartData($companyId)
    {
        $fourYearsAgo = now()->subYears(3)->startOfYear();
        $company = Company::where('company_code', $companyId)->first();
        
        $companyTarget = $company->company_code;
        
        $companyCode = in_array($companyTarget, [2000, 7000]) ? [2000, 7000] : [$companyTarget];

        $permanentQuery = DB::table('users')
            ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->whereIn('teams.company_code', $companyCode)
            ->where('pvt_members.status', '!=', 'gm')
            ->where('papers.status', 'accepted by innovation admin')
            ->where('events.year', '>=', $fourYearsAgo)
            ->select(
                DB::raw('events.year as year'),
                DB::raw('users.gender as gender'),
                DB::raw("CONCAT(pvt_members.employee_id, '-', teams.id) as unique_key")
            );

        $outsourcingQuery = DB::table('ph2_members')
            ->join('teams', 'ph2_members.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->where('papers.status', 'accepted by innovation admin')
            ->where('events.year', '>=', $fourYearsAgo)
            ->whereIn('teams.company_code', $companyCode)
            ->select(
                DB::raw('events.year as year'),
                DB::raw("'Outsource' as gender"),
                DB::raw("CONCAT(ph2_members.name, '-', teams.id) as unique_key")
            );

        $combined = $permanentQuery->unionAll($outsourcingQuery);

        $result = DB::table(DB::raw("({$combined->toSql()}) as combined"))
            ->mergeBindings($combined)
            ->select(
                'year',
                'gender',
                DB::raw('COUNT(DISTINCT unique_key) as total')
            )
            ->groupBy('year', 'gender')
            ->orderBy('year', 'asc')
            ->get()
            ->groupBy('year')
            ->map(function ($yearData) {
            $normalize = function ($g) {
                $x = strtolower(trim((string) $g));   
                if ($x === '' || in_array($x, ['male','laki-laki','laki','m','1','unknown','0'], true)) {
                    return 'Male';
                }
                if (in_array($x, ['female','perempuan','perempua','f'], true)) {
                    return 'Female';
                }
                if ($x === 'outsource') {
                    return 'Outsource';
                }
                return 'Male';
            };

            $grouped = $yearData->groupBy(function ($row) use ($normalize) {
                return $normalize($row->gender ?? '');
            });

            $male      = (int) ($grouped->get('Male', collect())->sum('total'));
            $female    = (int) ($grouped->get('Female', collect())->sum('total'));
            $outsource = (int) ($grouped->get('Outsource', collect())->sum('total'));

            return [
                'laki_laki'   => $male,
                'perempuan'   => $female,
                'outsourcing' => $outsource,
                'total'       => $male + $female + $outsource,
            ];
        })

            ->toArray();

        return $result;
    }

    private function fetchGrowthPerEventData($companyId): array
    {
        $fourYearsAgo = now()->subYears(3)->startOfYear();
        $company      = Company::where('company_code', $companyId)->firstOrFail();

        $target       = (int) $company->company_code;
        $companyCodes = in_array($target, [2000, 7000], true) ? [2000, 7000] : [$target];

        // Karyawan tetap/PKWT
        $permanent = DB::table('users')
            ->join('pvt_members', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
            ->join('pvt_event_teams as pet', 'pet.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pet.event_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->whereIn('teams.company_code', $companyCodes)
            ->where('pvt_members.status', '!=', 'gm')
            ->where('papers.status', 'accepted by innovation admin')
            ->where('events.year', '>=', $fourYearsAgo)
            ->select(
                'events.id as event_id',
                'events.event_name',
                'events.year',
                DB::raw("CONCAT(pvt_members.employee_id,'-',teams.id) as uniq")
            );

        // Outsource
        $outs = DB::table('ph2_members')
            ->join('teams', 'ph2_members.team_id', '=', 'teams.id')
            ->join('pvt_event_teams as pet', 'pet.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pet.event_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->where('papers.status', 'accepted by innovation admin')
            ->where('events.year', '>=', $fourYearsAgo)
            ->whereIn('teams.company_code', $companyCodes)
            ->select(
                'events.id as event_id',
                'events.event_name',
                'events.year',
                DB::raw("CONCAT(ph2_members.name,'-',teams.id) as uniq")
            );

        $union = $permanent->unionAll($outs);

        // Total per event per tahun
        $rows = DB::table(DB::raw("({$union->toSql()}) as u"))
            ->mergeBindings($union)
            ->select('event_id', 'event_name', 'year', DB::raw('COUNT(DISTINCT uniq) as total'))
            ->groupBy('event_id', 'event_name', 'year')
            ->orderBy('event_name', 'asc')
            ->orderBy('year', 'asc')
            ->get();

        // Kelompokkan per event_name lalu hitung growth YoY dalam kelompok tsb
        $grouped = $rows->groupBy('event_name');

        $out = [];
        foreach ($grouped as $eventName => $items) {
            // pastikan urut tahun
            $sorted = $items->sortBy('year')->values();

            foreach ($sorted as $idx => $r) {
                $curTotal = (int) $r->total;
                if ($idx === 0) {
                    $out[] = [
                        'event'      => $eventName,
                        'year'       => (int) $r->year,
                        'total'      => $curTotal,
                        'growth_abs' => null,
                        'growth_pct' => null,
                    ];
                } else {
                    $prevTotal = (int) $sorted[$idx - 1]->total;
                    $diff      = $curTotal - $prevTotal;
                    $pct       = $prevTotal > 0 ? round(($diff / $prevTotal) * 100, 1) : null;

                    $out[] = [
                        'event'      => $eventName,
                        'year'       => (int) $r->year,
                        'total'      => $curTotal,
                        'growth_abs' => $diff,
                        'growth_pct' => $pct,
                    ];
                }
            }
        }

        // Optional: urutkan output final (event asc, year asc)
        usort($out, function ($a, $b) {
            return [$a['event'], $a['year']] <=> [$b['event'], $b['year']];
        });

        return $out;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.company.total-innovator-with-gender-chart', [
            'chartData' => $this->chartData,
            'company_name' => $this->companyName,
            'growthPerEventData'  => $this->fetchGrowthPerEventData(request('company_id') ?? null),
        ]);
    }
}