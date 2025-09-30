<?php

namespace App\View\Components\Dashboard;

use App\Models\Company;
use App\Models\Paper;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class FinancialBenefitTotalChart extends Component
{
    public $chartData;
    public $isSuperadmin;
    public $userCompanyCode;
    public $title;
    public $filteredCompanyCode;

    public function __construct($isSuperadmin, $userCompanyCode)
    {
        $this->isSuperadmin = $isSuperadmin;
        $this->userCompanyCode = $userCompanyCode;

        // Tentukan filteredCompanyCode
        $this->filteredCompanyCode = in_array($userCompanyCode, [2000, 7000])
            ? [2000, 7000]
            : [$userCompanyCode];

        // Set judul berdasarkan hak akses
        $this->title = $isSuperadmin
            ? "Semua perusahaan"
            : Company::where('company_code', $userCompanyCode)->value('company_name');

        $this->chartData = $this->getChartData();
    }

    private function getChartData()
    {
        $query = Paper::select(
            'events.year as year',
            DB::raw('SUM(papers.financial) as total_financial')
        )
        ->join('teams', 'papers.team_id', '=', 'teams.id')
        ->join('pvt_event_teams', 'pvt_event_teams.team_id', '=', 'teams.id')
        ->join('events', 'events.id', '=', 'pvt_event_teams.event_id');

        // Filter company jika bukan superadmin
        if (!$this->isSuperadmin) {
            $query->whereIn('teams.company_code', $this->filteredCompanyCode);
        }

        $yearlyTotals = $query->groupBy('events.year')
            ->orderBy('events.year')
            ->get();

        return [
            'labels' => $yearlyTotals->pluck('year'),
            'data' => $yearlyTotals->pluck('total_financial'),
        ];
    }

    public function render()
    {
        return view('components.dashboard.financial-benefit-total-chart', [
            'chartData' => $this->chartData,
            'title' => $this->title
        ]);
    }
}
