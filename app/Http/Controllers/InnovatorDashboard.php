<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Team;
use App\Models\Paper;
use App\Models\PvtMember;
use App\Models\PvtEventTeam;
use App\Models\Event;
use Log;

class InnovatorDashboard extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Memuat tim beserta paper dan event
        $teamIds = PvtMember::where('employee_id', $user->employee_id)->pluck('team_id');
        $teams = Team::with(['paper', 'pvtEventTeams.event'])->whereIn('id', $teamIds)->get();

        return view('innovator.dashboard.index', compact('user', 'teams'));
    }
}
