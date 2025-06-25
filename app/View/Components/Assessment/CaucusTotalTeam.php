<?php

namespace App\View\Components\Assessment;

use App\Models\PvtEventTeam;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use App\Models\pvtAssesmentTeamJudge;

class CaucusTotalTeam extends Component
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
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('pvt_assesment_team_judges.stage', 'caucus')
            ->groupBy('pvt_event_teams.id', 'teams.team_name', 'teams.category_id', 'categories.category_name')
            ->havingRaw('COUNT(*) = SUM(CASE WHEN score != 0 THEN 1 ELSE 0 END)')
            ->select(
                'pvt_event_teams.id as event_team_id',
                'teams.team_name',
                'teams.category_id',
                'categories.category_name'
            )
            ->get();
        $categoriesDataComplete = $completeAssessment->groupBy('category_name');

        $notCompleteAssessment = DB::table('pvt_assesment_team_judges')
            ->join('pvt_event_teams', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('pvt_assesment_team_judges.stage', 'caucus')
            ->groupBy('pvt_event_teams.id', 'teams.team_name', 'teams.category_id', 'categories.category_name')
            ->havingRaw('COUNT(*) != SUM(CASE WHEN score != 0 THEN 1 ELSE 0 END)')
            ->select(
                'pvt_event_teams.id as event_team_id',
                'teams.team_name',
                'teams.category_id',
                'categories.category_name'
            )
            ->get();
        $categoriesDataNotComplete = $notCompleteAssessment->groupBy('category_name');
        
        return view('components.assessment.caucus-total-team', [
            'totalCompleteAssessment' => $completeAssessment->count(),
            'categoriesDataComplete' => $categoriesDataComplete,
            'categoriesDataNotComplete' => $categoriesDataNotComplete,
            'totalNotCompleteAssessment' => $notCompleteAssessment->count(),
            'totalTeams' => $notCompleteAssessment->count() + $completeAssessment->count(),
        ]);
    }
}