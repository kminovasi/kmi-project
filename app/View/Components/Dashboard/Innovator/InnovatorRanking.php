<?php

namespace App\View\Components\Dashboard\Innovator;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InnovatorRanking extends Component
{
    public $innovatorData;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($userCompanyCode, $isSuperadmin)
    {
        $userRole = Auth::user()->role;
        $filteredCompanyCode = [];
        if (in_array($userCompanyCode, [2000, 7000])) {
            $filteredCompanyCode = [2000, 7000];
        } else {
            $filteredCompanyCode = [$userCompanyCode];
        }

        $this->innovatorData = DB::table('pvt_members')
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->leftJoin('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->select(
                'pvt_members.employee_id',
                'users.name as employee_name',
                DB::raw('COUNT(DISTINCT teams.id) as total_teams'),
                'events.year'
            )
            ->where('papers.status', 'accepted by innovation admin')
            ->whereIn('pvt_members.status', ['member', 'leader'])
            ->when($userRole != 'Superadmin', function ($query) use ($filteredCompanyCode) {
                $query->whereIn('teams.company_code', $filteredCompanyCode);
            })
            ->groupBy('pvt_members.employee_id', 'users.name', 'events.year')
            ->get()
            ->groupBy('year')  
            ->map(function ($groupedByYear) {
                return $groupedByYear
                    ->sortByDesc('total_teams')
                    ->take(10)
                    ->map(function ($item) {
                        return [
                            'name' => $item->employee_name,
                            'total' => $item->total_teams,
                        ];
                    });
            });
    
    // dd($this->innovatorData);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.dashboard.innovator.innovator-ranking', [
            'innovatorData' => $this->innovatorData
        ]);
    }
}