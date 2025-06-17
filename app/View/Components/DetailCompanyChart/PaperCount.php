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

        foreach ($availableYears as $year) {
            $totalPapers = DB::table('papers')
                ->join('teams', 'teams.id', '=', 'papers.team_id')
                ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
                ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
                ->whereIn('teams.company_code', $filteredCodes)
                ->where('papers.status', 'accepted by innovation admin')
                ->whereYear('events.year', $year)
                ->count();

            $yearlyPapers[$year] = $totalPapers;
        }

        $this->chartData = json_encode([
            'years' => array_keys($yearlyPapers),
            'paperCounts' => array_values($yearlyPapers),
        ]);
    }

    public function render()
    {
        return view('components.detail-company-chart.paper-count');
    }
}