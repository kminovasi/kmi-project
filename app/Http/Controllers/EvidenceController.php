<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\PvtEventTeam;
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
        $eventId = 50; // get event id from request

        $winningTeams = \DB::table('teams')
            ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
            ->join('papers', 'teams.id', '=', 'papers.team_id')
            ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
            ->where('teams.category_id', $id)
            ->where('events.status', 'finish')
            ->get();

        //     ->when($eventId, function ($query, $eventId) {
        //         return $query->where('pvt_event_teams.event_id', $eventId); // Filter berdasarkan event_id jika tersedia
        //     })
        // //     ->select('teams.id as team_id', 'teams.team_name', 'teams.company_code', 'pvt_event_teams.final_score', 'papers.innovation_title')

        $events = Event::where('status', 'finish')
            ->select('id', 'event_name', 'year')
            ->get();

        dd($winningTeams);

        return view('auth.admin.dokumentasi.evidence.list-innovations' , compact('winningTeams'));
    }

    function team_detail()
    {

        return view('auth.admin.dokumentasi.evidence.detail-team');
    }
}
