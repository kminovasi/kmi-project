<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TotalTeamCard extends Component
{
    public $teamDataInternal;
    public $teamDataGroup;

    public function __construct()
    {
        // Ambil data total tim yang diterima dalam 4 tahun terakhir
        $this->teamDataInternal = $this->getTotalTeamsByYear('internal');
        $this->teamDataGroup = $this->getTotalTeamsByYear('group');
    }

    private function getTotalTeamsByYear($eventType)
    {
        // Ambil 4 tahun terakhir
        $years = range(Carbon::now()->year - 3, Carbon::now()->year);

        $teamCounts = DB::table('teams')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->select(
                'events.year as year',
                DB::raw('COUNT(DISTINCT teams.id) as total_teams')
            )
            ->where('papers.status', 'accepted by innovation admin')
            ->where('events.type', $eventType)
            ->whereIn('events.year', $years)
            ->groupBy('events.year')
            ->orderBy('events.year')
            ->get();

        // Pastikan semua tahun memiliki data
        $result = collect($years)->mapWithKeys(function ($year) use ($teamCounts) {
            $count = $teamCounts->firstWhere('year', $year)?->total_teams ?? 0;
            return [$year => $count];
        })->sortKeys();

        return $result;
    }

    public function render()
    {
        return view('components.dashboard.total-team-card', [
            'teamDataInternal' => $this->teamDataInternal,
            'teamDataGroup' => $this->teamDataGroup
        ]);
    }
}