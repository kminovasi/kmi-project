<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventTeamController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('created_at', 'desc')->get();
        return view('event-team.index', compact('events'));
    }
}
