<?php

namespace App\View\Components\DetailCompanyChart;

use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Event;
use App\Models\Paper;
use Illuminate\View\Component;

class PaperCount extends Component
{
    public $companyName;
    public $chartData;

    public function __construct($companyId = null)
    {
        $company = Company::select('company_name', 'company_code')->where('id', $companyId)->first();
        $this->companyName = $company->company_name;

        $availableYears = Event::select('year')
            ->groupBy('year')
            ->orderBy('year', 'ASC')
            ->pluck('year')
            ->toArray();

        $yearlyPapers = [];

        $targetCompanyCode = $company->company_code;

        if (in_array($targetCompanyCode, [2000, 7000])) {
            $filteredCodes = [2000, 7000];
        } else {
            $filteredCodes = [$targetCompanyCode];
        }

        $teamYears = DB::table('teams')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('papers.status', 'accepted by innovation admin')
            ->whereIn('events.year', $availableYears)
            ->whereIn('teams.company_code', $filteredCodes)
            ->select('teams.id as team_id', 'events.year')
            ->get();

        // Group per tahun dan hitung jumlah unik team_id
        $teamCountsPerYear = $teamYears
            ->map(fn($row) => ['team_id' => $row->team_id, 'year' => $row->year])
            ->unique(fn($row) => $row['team_id'] . '-' . $row['year'])
            ->groupBy('year')
            ->map(fn($group) => count($group));

        // Bikin hasil akhir sesuai urutan $availableYears
        $result = collect($availableYears)->mapWithKeys(function ($year) use ($teamCountsPerYear) {
            return [$year => $teamCountsPerYear[$year] ?? 0];
        })->sortKeys();

        $this->chartData = json_encode([
            'years' => $result->keys()->toArray(),
            'paperCounts' => $result->values()->toArray(),
        ]);
    }

    public function render()
    {
        return view('components.detail-company-chart.paper-count');
    }
}