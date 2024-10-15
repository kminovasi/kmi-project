<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Paper;
use App\Models\PvtEventTeam;
use App\Models\PvtMember;
use App\Models\Team;
use App\Models\Theme;
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
        // Filter dan Pencarian
        $search = $request->input('search');
        // $status = $request->input('status');
        $theme = $request->input('theme');
        $event = $request->input('event');

        $papers = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->where('teams.category_id', $id)
            ->where('events.status', 'finish')
            ->select('papers.*', 'teams.team_name', 'pvt_event_teams.*', 'events.event_name', 'events.year', 'themes.theme_name');

            // Filter berdasarkan judul paper (pencarian)
        if ($search) {
            $papers = $papers->where('papers.innovation_title', 'ILIKE', '%' . $search . '%');
        }

        // Filter berdasarkan status progress
        // if ($status) {
        //     $papers = $papers->where('papers.status_progress', 'ILIKE', $status);
        // }

        // Filter berdasarkan theme
        if ($theme) {
            $papers = $papers->where('teams.theme_id', '=', $theme);
        }

        if ($event) {
            $papers = $papers->where('pvt_event_teams.event_id', '=', $event);
        }

        $papers = $papers->paginate(10);

        $themes = Theme::all();

        $events = Event::where('status', 'finish')
            ->select('id', 'event_name', 'year')
            ->get();

        return view('auth.admin.dokumentasi.evidence.list-innovations' , compact('papers', 'events', 'themes', 'category'));
    }

    function paper_detail($id)
    {

        $team = Team::findOrFail($id);
        // Ambil tim berdasarkan team_id

        $papers = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->where('papers.team_id', $id)
            ->get();

        // dd($papers);

        $teamMember = $team->pvtMembers()->with('user')->get();


        return view('auth.admin.dokumentasi.evidence.detail-team', compact('teamMember', 'papers'));
    }
}
