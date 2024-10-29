<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Event;
use App\Models\Judge;
use Auth;
use Illuminate\Http\Request;

class JuriController extends Controller
{
    function index(Request $request)
    {

        $user = Auth::user();
        $companyCode = $user->company_code;

        if ($user->role == 'Admin') {

            $judges = Judge::with('event')
            ->join('users', 'judges.employee_id', '=', 'users.employee_id')
            ->where('users.company_code', $companyCode)
            ->select(
                'judges.*',
                'users.name',
                'users.employee_id',
                'users.company_name',
                'users.company_code',
                'users.unit_name',)
            ->paginate(10);

        } else {

            $company = request()->input('company');
            $event = request()->input('event');
            $search = request()->input('search');

            $judges = Judge::with('event')
            ->join('users', 'judges.employee_id', '=', 'users.employee_id')
            ->select(
                'judges.*',
                'users.name',
                'users.employee_id',
                'users.company_name',
                'users.company_code',
                'users.unit_name',);

            if ($company) {
                $judges = $judges->where('company_code', $company);
            }

            if ($event) {
                $judges = $judges->where('event_id', $event);
            }

            if ($search) {
                $judges = $judges->where('users.name', 'ILIKE', '%' . $search . '%');
            }

            $judges = $judges->paginate(10);
        }

        $companies = Company::all();
        $events = Event::all();

        return view('auth.admin.management_system.assign-juri.index', compact('judges', 'events', 'companies'));
    }

    function create() {}

    function store() {}

    function destroy() {}

    function edit() {}
}
