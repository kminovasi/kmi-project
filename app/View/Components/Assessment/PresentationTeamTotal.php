<?php

namespace App\View\Components\Assessment;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        $employeeId = Auth::user()->employee_id;
        $isSuperadmin = strtolower(Auth::user()->role) === 'superadmin';
        
        $completeAssessment = DB::table('pvt_assesment_team_judges')
            ->join('judges', 'judges.id', '=', 'pvt_assesment_team_judges.judge_id')
            ->join('pvt_event_teams', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('pvt_assesment_team_judges.stage', 'presentation')
            ->when(!$isSuperadmin, function ($query) use ($employeeId) {
                $query->where('judges.employee_id', $employeeId);
            })
            ->groupBy(
                'pvt_event_teams.id',
                'teams.team_name',
                'teams.category_id',
                'categories.category_name'
            )
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
            ->join('judges', 'judges.id', '=', 'pvt_assesment_team_judges.judge_id')
            ->join('users', 'users.employee_id', '=', 'judges.employee_id')
            ->join('pvt_event_teams', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('pvt_assesment_team_judges.stage', 'presentation')
            ->where('pvt_assesment_team_judges.score', 0)
            ->when(!$isSuperadmin, function ($query) use ($employeeId) {
                $query->where('judges.employee_id', $employeeId);
            })
            ->select(
                'teams.team_name',
                'categories.category_name',
                'users.name as judge_name'
            )
            ->get();
        
        $categoriesDataNotComplete = $notCompleteAssessment
            ->groupBy('category_name')
            ->map(function ($teams) {
                return $teams->groupBy('team_name')->map(function ($judges) {
                    return $judges->unique('judge_name'); // hilangkan duplikat juri per tim (just in case)
                });
            });

        return view('components.assessment.presentation-team-total', [
            'totalCompleteAssessment' => $completeAssessment->pluck('team_name')->unique()->count(),
            'categoriesDataComplete' => $categoriesDataComplete,
            'categoriesDataNotComplete' => $categoriesDataNotComplete,
            'totalNotCompleteAssessment' => $notCompleteAssessment->pluck('team_name')->unique()->count(),
            'totalTeams' => $notCompleteAssessment->pluck('team_name')->unique()->count() + $completeAssessment->pluck('team_name')->unique()->count(),
        ]);
    }
}