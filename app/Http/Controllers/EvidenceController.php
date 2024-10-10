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

        $winningTeams = \DB::table('teams')
        ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
        ->join('papers', 'teams.id', '=', 'papers.team_id')
        ->where('teams.category_id', $id)
        ->get();

        // dd($winningTeams);

        return view('auth.admin.dokumentasi.evidence.list-winner');
    }

    function team_detail()
    {

        return view('auth.admin.dokumentasi.evidence.detail-team');
    }

}
