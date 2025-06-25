<?php

namespace App\View\Components\Assessment;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Mpdf\Tag\Bdi;

class DeviationInformation extends Component
{
    public $assignmentJudgeData;
    /**
     * Create a new component instance.
     * 
     * @return void
     */
    public function __construct($eventTeamId, $assessmentStage)
    {
        $this->assignmentJudgeData = DB::table('pvt_assesment_team_judges')
            ->join('judges', 'pvt_assesment_team_judges.judge_id', '=', 'judges.id')
            ->join('users', 'judges.employee_id', '=', 'users.employee_id')
            ->where('pvt_assesment_team_judges.event_team_id', $eventTeamId)
            ->where('pvt_assesment_team_judges.stage', $assessmentStage)
            ->select('users.name as judge_name', DB::raw('SUM(pvt_assesment_team_judges.score) as total_score'), 'pvt_assesment_team_judges.judge_id')
            ->groupBy('pvt_assesment_team_judges.judge_id', 'users.name')
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.assessment.deviation-information', [
            'assignmentJudgeData' => $this->assignmentJudgeData
        ]);
    }
}