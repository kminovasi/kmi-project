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

        return view('auth.admin.dokumentasi.evidence.index' , compact('categories'));
    }


    function list_winner($id)
    {
        $categoryId = $id;
        // $eventId = 50; // get event id from request

        $winningTeams = \DB::table('teams')
        ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
        ->join('papers', 'teams.id', '=', 'papers.team_id')
        ->where('teams.category_id', $id)
        // ->when($eventId, function ($query, $eventId) {
        //     return $query->where('pvt_event_teams.event_id', $eventId); // Filter berdasarkan event_id jika tersedia
        // })
        ->get();

        dd($winningTeams);

        return view('auth.admin.dokumentasi.evidence.list-winner');
    }

    function team_detail()
    {

        return view('auth.admin.dokumentasi.evidence.detail-team');
    }

}
