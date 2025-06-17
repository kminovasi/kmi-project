<?php

namespace App\View\Components\Assessment;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class PresentationTeamTotal extends Component
{
    public $eventId;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $completeAssessment = DB::table('pvt_assesment_team_judges')
            ->join('pvt_event_teams', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('pvt_assesment_team_judges.stage', 'presentation')
            ->groupBy('pvt_event_teams.id', 'teams.team_name') // Perlu group by semua kolom non-agregat
            ->havingRaw('COUNT(*) = SUM(CASE WHEN score != 0 THEN 1 ELSE 0 END)')
            ->select('pvt_event_teams.id as event_team_id', 'teams.team_name as team_name')
            ->get();

        $notCompleteAssessment = DB::table('pvt_assesment_team_judges')
            ->join('pvt_event_teams', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('pvt_assesment_team_judges.stage', 'presentation')
            ->groupBy('pvt_event_teams.id', 'teams.team_name') // Perlu group by semua kolom non-agregat
            ->havingRaw('COUNT(*) != SUM(CASE WHEN score != 0 THEN 1 ELSE 0 END)')
            ->select('pvt_event_teams.id as event_team_id', 'teams.team_name as team_name')
            ->get();
        
        $totalTeams = $notCompleteAssessment->count() + $completeAssessment->count();

        return view('components.assessment.presentation-team-total', [
            'totalCompleteAssessment' => $completeAssessment->count(),
            'completeAssessment' => $completeAssessment,
            'notCompleteAssessment' => $notCompleteAssessment,
            'totalNotCompleteAssessment' => $notCompleteAssessment->count(),
            'totalTeams' => $totalTeams,
        ]);
    }
}