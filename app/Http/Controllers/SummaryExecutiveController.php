<?php

namespace App\Http\Controllers;

use App\Models\PvtEventTeam;
use App\Models\SummaryExecutive;
use Illuminate\Http\Request;
use Log;

class SummaryExecutiveController extends Controller
{
    public function getSummaryExecutiveByEventTeamId($id)
    {
        $summaryExecutive = SummaryExecutive::with([
            'pvtEventTeam.team' => function ($query) {
                $query->select('id', 'team_name', 'company_code'); // Mendapatkan nama tim dan kode perusahaan
            },
            'pvtEventTeam.team.company' => function ($query) {
                $query->select('company_code', 'company_name'); // Mendapatkan nama perusahaan
            },
            'pvtEventTeam.team.paper' => function ($query) {
                $query->select('team_id', 'innovation_title'); // Mendapatkan judul inovasi
            }
        ])->where('pvt_event_teams_id', $id)->first();

        // Struktur JSON yang diinginkan
        return response()->json([
            'team_name' => $summaryExecutive->pvtEventTeam->team->team_name,
            'innovation_title' => $summaryExecutive->pvtEventTeam->team->paper->innovation_title,
            'company_name' => $summaryExecutive->pvtEventTeam->team->company->company_name,
            'pvt_event_teams_id' => $summaryExecutive->pvtEventTeam->id,
            'summary_executives_id' => $summaryExecutive->id,
            'problem_background' => $summaryExecutive->problem_background,
            'innovation_idea' => $summaryExecutive->innovation_idea,
            'benefit' => $summaryExecutive->benefit,
        ]);
    }

    public function getSummaryByTeamAndEventTeam($team_id, $pvt_event_teams_id)
    {
        $summary = PvtEventTeam::join('teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('companies', 'teams.company_code', '=', 'companies.company_code')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->leftJoin('summary_executives', 'pvt_event_teams.id', '=', 'summary_executives.pvt_event_teams_id')
            ->where('teams.id', $team_id)
            ->where('pvt_event_teams.id', $pvt_event_teams_id)
            ->select(
                'teams.id as team_id',
                'papers.innovation_title',
                'teams.team_name',
                'companies.company_name',
                'pvt_event_teams.id as pvt_event_teams_id',
                'summary_executives.problem_background',
                'summary_executives.innovation_idea',
                'summary_executives.benefit'
            )
            ->first();
        
        if (!$summary) {
            return response()->json([
                'pvt_event_teams_id' => $pvt_event_teams_id,
                'problem_background' => '',
                'innovation_idea' => '',
                'benefit' => '',
            ]);
        }
    
        return response()->json($summary ?: []);
    }
}
