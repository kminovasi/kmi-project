<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PvtMember;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        // Memuat tim beserta paper dan event
        $teamIds = PvtMember::where('employee_id', $user->employee_id)->pluck('team_id');
        $teams = Team::with(['paper', 'pvtEventTeams.event'])->whereIn('id', $teamIds)->get();

        // Memuat event aktif yang sedang diikuti
        $activeEvents = Event::whereHas('pvtEventTeams', function ($query) use ($teamIds) {
            $query->whereIn('team_id', $teamIds);
        })->where('status', 'active')->get();

        if(Session::get('data_query') != NULL){
            $data_query = Session::get('data_query');
            Session::forget('data_query');
            // var_dump(Session('data_query'));
            $manager = User::where('employee_id',$data_query[0]->manager_id)->first();

            $_arr = [
                'name' => $data_query[0]->name,
                'manager' => $manager->name,
                'email' => $data_query[0]->email,
                'position' => $data_query[0]->position_title,
                'company' => $data_query[0]->company_name,
                'directorate' => $data_query[0]->directorate_name,
                'department' => $data_query[0]->department_name,
                'unit' => $data_query[0]->unit_name,
                'section' => $data_query[0]->section_name,
                'jobLevel' => $data_query[0]->job_level,
            ];


        }else{
            $manager = User::where('employee_id',auth()->user()->manager_id)->first();

            $_arr = [
                'name' => auth()->user()->name,
                'manager' => $manager->name,
                'email' => auth()->user()->email,
                'position' => auth()->user()->position_title,
                'company' => auth()->user()->company_name,
                'directorate' => auth()->user()->directorate_name,
                'department' => auth()->user()->department_name,
                'unit' => auth()->user()->unit_name,
                'section' => auth()->user()->section_name,
                'jobLevel' => auth()->user()->job_level,
            ];
        }
        return view('auth.user.profile.index', compact('user', 'teams', 'activeEvents'))->with($_arr);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
