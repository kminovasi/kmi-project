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

    public function __construct($isSuperadmin, $userCompanyCode)
    {
        $this->isSuperadmin = $isSuperadmin;
        $this->userCompanyCode = $userCompanyCode;
        $this->chartData = $this->getChartData();
        if ($isSuperadmin) {
            $this->title = "Semua perusahaan";
        } else {
            $company = Company::where('company_code', $userCompanyCode)->first();
            $companyName = $company->company_name;
            $this->title = $companyName;
        }
    }

    private function getChartData()
    {
        $query = Paper::select(
            DB::raw('EXTRACT(YEAR FROM papers.created_at) as year'),
            DB::raw('SUM(papers.financial) as total_financial')
        )
            ->join('teams', 'papers.team_id', '=', 'teams.id');

        // Filter berdasarkan company code jika bukan superadmin
        if (!$this->isSuperadmin) {
            $query->where('teams.company_code', $this->userCompanyCode);
        }

        $yearlyTotals = $query->groupBy(DB::raw('EXTRACT(YEAR FROM papers.created_at)'))
            ->orderBy('year')
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
