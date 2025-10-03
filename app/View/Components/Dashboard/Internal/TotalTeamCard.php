<?php

namespace App\View\Components\Dashboard\Internal;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Log;

class TotalTeamCard extends Component
{
    public $teamData;

    public function __construct()
    {
        $this->teamData = $this->getTotalTeamsByYear();
    }

    private function getTotalTeamsByYear()
    {
        $companyCode = Auth::user()->company_code;
        $years = range(Carbon::now()->year - 2, Carbon::now()->year);
        $paperStatus = 'accepted by innovation admin';
        
        if(in_array($companyCode, [2000, 7000])) {
            $filteredCompanyCode = [2000, 7000];
        } else {
            $filteredCompanyCode = [$companyCode];
        }

        $teamCounts = DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->select(
                'events.year as year',
                DB::raw('COUNT(DISTINCT teams.id) as total_teams')
            )
            ->where('papers.status', $paperStatus)
            ->whereIn('teams.company_code', $filteredCompanyCode)
            ->whereIn('events.year', $years)
            ->groupBy('events.year')
            ->orderBy('year')
            ->get();

        $result = collect($years)->mapWithKeys(function ($year) use ($teamCounts) {
            $count = $teamCounts->firstWhere('year', $year)?->total_teams ?? 0;
            return [$year => $count];
        })->sortKeys();

        return $result;
    }

    public function render()
    {
        return view('components.dashboard.internal.total-team-card', [
            'teamData' => $this->teamData,
            'company_code' => Auth::user()->company_code
        ]);
    }
}