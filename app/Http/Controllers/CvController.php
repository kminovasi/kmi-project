<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CvController extends Controller
{
    function index () {
        // get current user employee_id
        $employee_id = Auth::user()->employee_id;

        $innovations = \DB::table('pvt_members')
        ->join('teams', 'pvt_members.team_id', '=', 'teams.id')
        ->join('papers', 'teams.id', '=', 'papers.team_id')
        ->where('pvt_members.employee_id', $employee_id)
        ->join('pvt_event_teams', 'teams.id', '=', 'pvt_event_teams.team_id')
        ->join('events', 'pvt_event_teams.event_id', '=', 'events.id')
        ->join('themes', 'teams.theme_id', '=', 'themes.id')
        ->select(
            'papers.*',
            'teams.team_name',
            'teams.status_lomba',
            'events.event_name',
            'events.year',
            'pvt_event_teams.status as event_status',
            'themes.id',
            'themes.theme_name',
            'pvt_event_teams.*'
        )
        ->get();


        dd($innovations);

        // $innovation = /DB::

        return view('auth.admin.dokumentasi.cv.index', compact('innovations'));
    }
}
