<?php

namespace App\Exports;

use App\Models\Judge;
use Auth;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class JuriExport implements FromView
{

    public function __construct()
    {

    }

    public function view(): View
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
                    'users.unit_name',
                )
                ->get();
        } else {
            $judges = Judge::with('event')
                ->join('users', 'judges.employee_id', '=', 'users.employee_id')
                ->select(
                    'judges.*',
                    'users.name',
                    'users.employee_id',
                    'users.company_name',
                    'users.company_code',
                    'users.unit_name',
                )->get();

            }

        return view('auth.admin.management_system.assign-juri.export', compact('judges'));
    }
}
