<?php

namespace App\Http\Livewire\Assessment;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OndeskTeamTotal extends Component
{
    public $eventId;

    protected $listeners = ['eventChanged' => 'updateEvent'];

    public function mount($eventId)
    {
        $this->eventId = $eventId;
    }

    public function updateEvent($eventId)
    {
        logger("updateEvent() dipanggil dengan eventId = $eventId");
        $this->eventId = $eventId;
    }

    public function render()
    {
        $employeeId = Auth::user()->employee_id;
        $role = strtolower(Auth::user()->role);
        $isSuperadmin = in_array($role, ['superadmin', 'admin']);

        $completeAssessment = DB::table('pvt_assesment_team_judges')
            ->join('judges', 'judges.id', '=', 'pvt_assesment_team_judges.judge_id')
            ->join('pvt_event_teams', 'pvt_event_teams.id', '=', 'pvt_assesment_team_judges.event_team_id')
            ->join('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('categories', 'categories.id', '=', 'teams.category_id')
            ->where('pvt_event_teams.event_id', $this->eventId)
            ->where('pvt_assesment_team_judges.stage', 'on desk')
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
            ->where('pvt_assesment_team_judges.stage', 'on desk')
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
                    return $judges->unique('judge_name');
                });
            });
        
        $teamIds = DB::table('pvt_assesment_team_judges as paj')
            ->join('judges', 'judges.id', '=', 'paj.judge_id')
            ->where('judges.employee_id', $employeeId)
            ->where('stage', 'on desk')
            ->pluck('paj.event_team_id');
        
        $allTeams = DB::table('pvt_event_teams')
            ->leftJoin('teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->leftJoin('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->leftJoin('categories', 'teams.category_id', '=', 'categories.id')
            ->leftJoin('minimumscore_events as ms_idea', function ($join) {
                $join->on('ms_idea.event_id', '=', 'events.id')
                     ->where('ms_idea.category', '=', 'IDEA');
            })
            ->leftJoin('minimumscore_events as ms_biii', function ($join) {
                $join->on('ms_biii.event_id', '=', 'events.id')
                     ->where('ms_biii.category', '=', 'BI/II');
            })
            ->where('events.id', $this->eventId)
            ->where('pvt_event_teams.status', '!=', 'tidak Lolos On Desk')
            ->when(!$isSuperadmin, function ($query) use ($teamIds) {
                $query->whereIn('pvt_event_teams.id', $teamIds);
            })
            ->select(
                'teams.*',
                'pvt_event_teams.*',
                'categories.category_name',
                'categories.category_parent',
                DB::raw("
                    CASE 
                        WHEN categories.category_name = 'IDEA BOX' THEN ms_idea.score_minimum_oda
                        ELSE ms_biii.score_minimum_oda
                    END AS score_minimum
                ")
            )
            ->get();

        $passedTeams = $allTeams->filter(function ($team) {
            return $team->score_minimum !== null && $team->total_score_on_desk >= $team->score_minimum;
        });
        
        $failedTeams = $allTeams->filter(function ($team) {
            return $team->score_minimum !== null && $team->total_score_on_desk < $team->score_minimum;
        });

        $categoriesDataFailed = $failedTeams->groupBy('category_name');
        $categoriesDataPassed = $passedTeams->groupBy('category_name');

        return view('livewire.assessment.ondesk-team-total', [
            'totalCompleteAssessment' => $completeAssessment->pluck('team_name')->unique()->count(),
            'categoriesDataComplete' => $categoriesDataComplete,
            'categoriesDataNotComplete' => $categoriesDataNotComplete,
            'totalNotCompleteAssessment' => $notCompleteAssessment->pluck('team_name')->unique()->count(),
            'totalTeams' => $notCompleteAssessment->pluck('team_name')->unique()->count() + $completeAssessment->pluck('team_name')->unique()->count(),
            'passedTeams' => $passedTeams,
            'failedTeams' => $failedTeams,
            'categoriesDataPassed' => $categoriesDataPassed,
            'categoriesDataFailed' => $categoriesDataFailed
        ]);
    }
}