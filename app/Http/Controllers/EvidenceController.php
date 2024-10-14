<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Paper;
use App\Models\PvtEventTeam;
use App\Models\PvtMember;
use App\Models\Team;
use Illuminate\Http\Request;

class EvidenceController extends Controller
{
    function index()
    {
        $categories = Category::all();

        return view('auth.admin.dokumentasi.evidence.index', compact('categories'));
    }


    function list_winner($id)
    {

        $papers = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->join('themes', 'teams.theme_id', '=', 'themes.id')
            ->where('teams.category_id', $id)
            ->where('events.status', 'finish')
            ->select('papers.*', 'teams.team_name', 'pvt_event_teams.final_score', 'events.event_name', 'events.year', 'themes.theme_name')
            ->get();

        // dd($papers);

        //     ->when($eventId, function ($query, $eventId) {
        //         return $query->where('pvt_event_teams.event_id', $eventId); // Filter berdasarkan event_id jika tersedia
        //     })

        $events = Event::where('status', 'finish')
            ->select('id', 'event_name', 'year')
            ->get();

        // dd($papers);

        return view('auth.admin.dokumentasi.evidence.list-innovations' , compact('papers'));
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
