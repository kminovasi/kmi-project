<?php

namespace App\Exports;

use App\Models\Team;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DetailPaperExport implements FromView
{

    protected $teamId;

    public function __construct($teamId)
    {
        $this->teamId = $teamId;
    }

    public function view(): View
    {
        $team = Team::findOrFail($this->teamId);

        // Ambil tim berdasarkan team_id
        $papers = \DB::table('teams')
            ->leftJoin('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->leftJoin('papers', 'teams.id', '=', 'papers.team_id')
            ->leftJoin('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->leftJoin('themes', 'teams.theme_id', '=', 'themes.id')
            ->leftJoin('document_supportings', 'papers.id', '=', 'document_supportings.paper_id')
            ->where('teams.id', $this->teamId)
            ->select(
                'papers.*',
                'pvt_event_teams.final_score',
                'pvt_event_teams.total_score_on_desk',
                'pvt_event_teams.total_score_presentation',
                'pvt_event_teams.total_score_caucus',
                'pvt_event_teams.is_best_of_the_best',
                'themes.theme_name',
                'events.event_name',
                'document_supportings.path'
            )
            ->limit(1)
            ->get();

        // mendapatkan data member berdasarkan id team
        $teamMember = $team->pvtMembers()->with('user')->get();


        return view('auth.admin.dokumentasi.evidence.export-detail', compact('papers', 'teamMember'));
    }
}