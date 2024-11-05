<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Team;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    function index()
    {
        $categories = Category::all();

        return view('auth.admin.dokumentasi.evidence.index', compact('categories'));
    }


    function List_paper($id, Request $request)
    {

        $category = Category::find($id);

        // mendapatkan semua data paper dengan kategori dan event yang status finish


        return view('auth.admin.dokumentasi.evidence.list-innovations', compact('category'));
    }

    function paper_detail($id)
    {

        $team = Team::findOrFail($id);

        // Ambil tim berdasarkan team_id
        $papers = \DB::table('teams')
            ->leftJoin('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->leftJoin('papers', 'teams.id', '=', 'papers.team_id')
            ->leftJoin('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->leftJoin('themes', 'teams.theme_id', '=', 'themes.id')
            ->leftJoin('document_supportings', 'papers.id', '=', 'document_supportings.paper_id')
            ->where('teams.id', $id)
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
            ->get();

        // mendapatkan data member berdasarkan id team
        $teamMember = $team->pvtMembers()->with('user')->get();


        return view('auth.admin.dokumentasi.evidence.detail-team', compact('teamMember', 'papers'));
    }
}
