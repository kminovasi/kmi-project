<?php

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TotalTeamChart extends Component
{
    public $teamData;

    public function __construct()
    {
        // Ambil data total tim yang diterima dalam 4 tahun terakhir
        $this->teamData = $this->getTotalTeamsByYear();
    }

    private function getTotalTeamsByYear()
    {
        // Ambil 4 tahun terakhir
        $years = range(Carbon::now()->year - 3, Carbon::now()->year);

        $teamCounts = DB::table('teams')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->select(
                DB::raw('EXTRACT(YEAR FROM teams.created_at) as year'),
                DB::raw('COUNT(DISTINCT teams.id) as total_teams')
            )
            ->where('papers.status', 'accepted by innovation admin')
            ->whereIn(DB::raw('EXTRACT(YEAR FROM teams.created_at)'), $years)
            ->groupBy(DB::raw('EXTRACT(YEAR FROM teams.created_at)'))
            ->orderBy('year')
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
        return view('components.dashboard.total-team-chart', [
            'teamData' => $this->teamData
        ]);
    }
}
