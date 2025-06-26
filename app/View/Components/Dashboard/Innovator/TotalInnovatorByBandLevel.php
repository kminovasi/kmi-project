<?php

namespace App\View\Components\Dashboard\Innovator;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class TotalInnovatorByBandLevel extends Component
{
    public $innovatorData;
    public $years;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($isSuperadmin, $userCompanyCode)
    {
        if( in_array($userCompanyCode, [2000, 7000])) {
            $filteredCompanyCode = [2000, 7000];
        } else {
            $filteredCompanyCode = [$userCompanyCode];
        }
        
        $rawData = DB::table('pvt_members')
            ->select(
                DB::raw('COUNT(DISTINCT CONCAT(pvt_members.employee_id, teams.id)) as total'),
                'events.year as year',
                'users.job_level as band_level'
            )
            ->join('teams', 'teams.id', '=', 'pvt_members.team_id')
            ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
            ->join('events', 'events.id', '=', 'pvt_event_teams.event_id')
            ->leftJoin('users', 'users.employee_id', '=', 'pvt_members.employee_id')
            ->join('papers', 'papers.team_id', '=', 'teams.id')
            ->when(!$isSuperadmin, function ($query) use ($filteredCompanyCode) {
                $query->whereIn('teams.company_code', $filteredCompanyCode);
            })
            ->whereIn('pvt_members.status', ['leader', 'member'])
            ->where('papers.status', 'accepted by innovation admin')
            ->groupBy('events.year', 'users.job_level')
            ->orderBy('users.job_level', 'ASC')
            ->get();
    
        // Ambil list tahun
        $this->years = $rawData->pluck('year')->unique()->sort()->values();
    
        // Bentuk data pivot
        $this->innovatorData = $rawData->groupBy('band_level')->map(function ($items) {
            return $items->pluck('total', 'year')->toArray();
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
        return view('components.dashboard.innovator.total-innovator-by-band-level', [
            'innovatorData' => $this->innovatorData,
            'years' => $this->years,
        ]);
    }
}