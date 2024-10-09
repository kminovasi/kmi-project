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
        // $teams = Team::where('category_id', $id)->get();

        // $events = Event::all();

        return view('auth.admin.dokumentasi.evidence.list-winner');
    }

    function team_detail()
    {
        // $teams = Team::where('id', $id)->first();

        return view('auth.admin.dokumentasi.evidence.detail-team');
    }

}
