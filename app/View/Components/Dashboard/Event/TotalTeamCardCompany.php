<?php

namespace App\View\Components\Dashboard\Event;

use App\Models\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class TotalTeamCardCompany extends Component
{
    public $totalTeams;

    /**
     * Create a new component instance.
     *
     * @param int $eventId
     */
    public function __construct($eventId)
    {
        $event = Event::find($eventId);

        $teams = DB::table('teams')
            ->join('companies', 'companies.company_code', '=', 'teams.company_code')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->where('pvt_event_teams.event_id', $eventId)
            ->select('companies.company_code', 'companies.company_name', 'teams.id as team_id')
            ->get()
            ->map(function ($team) use ($event) {
                // Jika tipe event adalah group dan company_code 7000, ubah ke 2000
                if ($event && $event->type === 'group' && $team->company_code == 7000) {
                    $team->company_code = 2000;
                    $team->company_name = 'PT Semen Indonesia (Persero)Tbk';
                }
                return $team;
            });
        
        // Group dan hitung tim berdasarkan company_code
        $this->totalTeams = $teams
            ->groupBy('company_code')
            ->map(function ($groupedTeams, $companyCode) {
                return [
                    'company_name' => $groupedTeams->first()->company_name,
                    'total_teams' => $groupedTeams->count(),
                ];
            })
            ->values(); // Reset index agar rapi

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.event.total-team-card-company', [
            'totalTeams' => $this->totalTeams,
        ]);
    }
}